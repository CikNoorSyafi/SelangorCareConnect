<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ProfileController;
use Barryvdh\DomPDF\Facade\Pdf;


Route::get('/auth', function () {
    return view('auth');
});

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', function () {
    return view('organizer.dashboard');
});

Route::get('/campaign', function () {
    return view('organizer.campaign');
});
Route::post('/campaign/store', [CampaignController::class, 'store']);

Route::get('/campaign', [CampaignController::class, 'index']);
Route::get(
    '/campaign/edit/{id}',
    [CampaignController::class, 'edit']
);

Route::post(
    '/campaign/update/{id}',
    [CampaignController::class, 'update']
);

Route::post(
    '/campaign/delete/{id}',
    [CampaignController::class, 'delete']
);



Route::get('/volunteers', [VolunteerController::class, 'index']);
Route::get('/volunteers/create', [VolunteerController::class, 'create']);
Route::post('/volunteers/store', [VolunteerController::class, 'store']);
Route::get('/volunteers/edit/{id}', [VolunteerController::class, 'edit']);
Route::post('/volunteers/update/{id}', [VolunteerController::class, 'update']);
Route::get('/volunteers/delete/{id}', [VolunteerController::class, 'delete']);
Route::get('/donation', [DonationController::class, 'index']);

Route::get('/donation/create', [DonationController::class, 'create']);
Route::post('/donation/store', [DonationController::class, 'store']);
Route::get('/donation/edit/{id}', [DonationController::class, 'edit']);
Route::post('/donation/update/{id}', [DonationController::class, 'update']);
Route::get('/donation/view/{id}', [DonationController::class, 'show']);
Route::get('/donation/delete/{id}', [DonationController::class, 'delete']);

Route::get(
    '/donation/allocation-details',
    [DonationController::class, 'allocationDetails']
);
Route::get(
    '/communication',
    [CommunicationController::class, 'index']
);

Route::get(
    '/communication/create',
    [CommunicationController::class, 'create']
);

Route::post(
    '/communication/store',
    [CommunicationController::class, 'store']
);

Route::get('/profile', [ProfileController::class, 'index']);
Route::get('/profile/edit', [ProfileController::class, 'edit']);
Route::post('/profile/update', [ProfileController::class, 'update']);
Route::get('/profile/password', [ProfileController::class, 'password']);
Route::post('/profile/password', [ProfileController::class, 'changePassword']);

Route::get('/donor/dashboard', function () {

    $campaigns =
        session(
            'campaigns',
            []
        );

    $history =
        session(
            'donation_history',
            []
        );

    foreach ($campaigns as &$campaign) {

        $campaign['collected'] = 0;

        foreach ($history as $transaction) {

            if (

                ($transaction['campaign'] ?? '') ==
                $campaign['name']

                &&

                $transaction['status'] == 'SUCCESS'

            ) {

                $campaign['collected'] +=
                    $transaction['amount'];

            }

        }

        $campaign['remaining'] =
            max(
                0,
                $campaign['target'] -
                $campaign['collected']
            );

        $campaign['progress'] =
            $campaign['target'] > 0

            ? round(
                (
                    $campaign['collected']
                    /
                    $campaign['target']
                ) * 100,
                1
            )

            : 0;
    }

    return view(
        'donor.dashboard',
        compact(
            'campaigns',
            'history'
        )
    );
});

Route::get('/donor/fund', function () {

    return view('donor.fund.index');

});

Route::post(
    '/donor/payment-gateway',
    function (\Illuminate\Http\Request $request) {

        $nextReference =
            session(
                'transaction_counter',
                1000
            ) + 1;

        $method = match ($request->input('payment_method')) {

            'card' => 'Credit / Debit Card',

            'fpx' => 'FPX Online Banking',

            'wallet' => 'E-Wallet',

        };

        session([
            'transaction_counter' => $nextReference,

            'payment_amount' =>
                $request->input('amount'),

            'payment_method' =>
                $method,

            'payment_reference' =>
                'SCC-' .
                now()->format('Ymd') .
                '-' .
                $nextReference,

            'campaign_name' =>
                $request->input(
                    'campaign_name',
                ),
        ]);

        return view(
            'donor.payment.gateway'
        );

    }
);

Route::get(
    '/donor/payment-confirmation',
    function () {

        return view(
            'donor.payment.payment-confirmation'
        );

    }
);

Route::post(
    '/donor/payment/success',
    function () {

        $history =
            session(
                'donation_history',
                []
            );

        $history[] = [

            'reference' => session('payment_reference'),

            'campaign' => session('campaign_name'),

            'amount' => session('payment_amount'),

            'method' => session('payment_method'),

            'status' => 'SUCCESS',

            'datetime' => now()->timezone(
                'Asia/Kuala_Lumpur'
            )

        ];

        session([
            'donation_history' => $history,

            'payment_status' => 'SUCCESS',

            'payment_datetime' =>
                now()->timezone(
                    'Asia/Kuala_Lumpur'
                ),
            'campaign' =>
                session('campaign_name'),
        ]);

        return redirect(
            '/donor/payment-confirmation'
        );

    }
);

Route::post(
    '/donor/payment/fail',
    function () {

        $history =
            session(
                'donation_history',
                []
            );

        $history[] = [

            'reference' => session('payment_reference'),

            'campaign' => session('campaign_name'),

            'amount' => session('payment_amount'),

            'method' => session('payment_method'),

            'status' => 'FAILED',

            'datetime' => now()->timezone(
                'Asia/Kuala_Lumpur'
            )

        ];

        session([
            'donation_history' => $history,

            'payment_status' => 'FAILED',

            'payment_datetime' =>
                now()->timezone(
                    'Asia/Kuala_Lumpur'
                ),
            'campaign' =>
                session('campaign_name'),
        ]);

        return redirect(
            '/donor/payment-confirmation'
        );

    }
);

Route::get(
    '/donor/download-receipt/{id}',
    function ($id) {

        $history =
            session(
                'donation_history',
                []
            );

        if (!isset($history[$id])) {

            return redirect(
                '/donor/history'
            );

        }

        $transaction =
            $history[$id];

        $data = [
            'campaign' =>
                $transaction['campaign'] ?? 'Community Fund',

            'amount' =>
                $transaction['amount'],

            'method' =>
                $transaction['method'],

            'reference' =>
                $transaction['reference'],

            'datetime' =>
                $transaction['datetime'],

            'status' =>
                $transaction['status']

        ];

        $pdf = Pdf::loadView(
            'donor.receipts.pdf',
            $data
        );

        return $pdf->download(
            'Receipt-' .
            $transaction['reference'] .
            '.pdf'
        );

    }
);

Route::get('/donor/donation', function () {

    $campaigns =
        session(
            'campaigns',
            []
        );

    return view(
        'donor.donation.index',
        compact('campaigns')
    );

});

Route::get(
    '/donor/history',
    function (\Illuminate\Http\Request $request) {

        $history =
            session(
                'donation_history',
                []
            );

        $status =
            $request->status;

        $search =
            $request->search;

        if ($status) {

            $history = array_filter(
                $history,
                function ($transaction) use ($status) {

                    return
                        strtolower(
                            $transaction['status']
                        ) ==
                        strtolower($status);

                }
            );

        }

        if ($search) {

            $history = array_filter(
                $history,
                function ($transaction) use ($search) {

                    $keyword =
                        strtolower($search);

                    return

                        str_contains(
                            strtolower(
                                $transaction['reference']
                            ),
                            $keyword
                        )

                        ||

                        str_contains(
                            strtolower(
                                $transaction['campaign']
                                ?? ''
                            ),
                            $keyword
                        )

                        ||

                        str_contains(
                            strtolower(
                                $transaction['method']
                            ),
                            $keyword
                        );

                }
            );

        }

        return view(
            'donor.history.index',
            compact(
                'history',
                'status',
                'search'
            )
        );

    }
);

Route::get(
    '/donor/history/view/{id}',
    function ($id) {

        $history =
            session(
                'donation_history',
                []
            );

        if (!isset($history[$id])) {
            return redirect(
                '/donor/history'
            );
        }

        session([
            'payment_reference' =>
                $history[$id]['reference'],

            'payment_amount' =>
                $history[$id]['amount'],

            'payment_method' =>
                $history[$id]['method'],

            'payment_status' =>
                $history[$id]['status'],

            'payment_datetime' =>
                $history[$id]['datetime'],

            'campaign_name' =>
                $history[$id]['campaign'] ?? 'Community Fund',
        ]);

        return redirect(
            '/donor/payment-confirmation'
        );

    }
);

Route::get('/donor/receipts', function () {
    return view('donor.receipts.index');
});

Route::get('/donor/profile', function () {
    return view('donor.profile.index');
});

