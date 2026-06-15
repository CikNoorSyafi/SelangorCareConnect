<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ProfileController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\VolunteerPortalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Models\Donation;
use App\Models\Campaign;

Route::view('/', 'welcome');

Route::get('/auth', function () {
    return view('auth');
});

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/profile', [ProfileController::class, 'index']);
Route::get('/profile/edit', [ProfileController::class, 'edit']);
Route::post('/profile/update', [ProfileController::class, 'update']);
Route::get('/profile/password', [ProfileController::class, 'password']);
Route::post('/profile/password', [ProfileController::class, 'changePassword']);

//// Organizer Portal Routes
Route::middleware('role:organizer')->group(function () {
    Route::get('/dashboard', function () {
        return view('organizer.dashboard');
    });
    Route::get('/campaign', [CampaignController::class, 'index']);
    Route::post('/campaign/store', [CampaignController::class, 'store']);


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
    Route::get(
        '/parameters',
        [ParameterController::class, 'index']
    );
    Route::post('/parameters/skills/store', [ParameterController::class, 'storeSkill']);
    Route::post('/parameters/skills/update/{id}', [ParameterController::class, 'updateSkill']);
    Route::post('/parameters/skills/delete/{id}', [ParameterController::class, 'deleteSkill']);
    Route::get('/parameters/skills/edit/{id}', [ParameterController::class, 'editSkill']);
    Route::post('/parameters/roles/store', [ParameterController::class, 'storeRole']);
    Route::get('/parameters/roles/edit/{id}', [ParameterController::class, 'editRole']);
    Route::post('/parameters/roles/update/{id}', [ParameterController::class, 'updateRole']);
    Route::post('/parameters/roles/delete/{id}', [ParameterController::class, 'deleteRole']);
    Route::post('/parameters/shifts/store', [ParameterController::class, 'storeShift']);
    Route::get('/parameters/shifts/edit/{id}', [ParameterController::class, 'editShift']);
    Route::post('/parameters/shifts/update/{id}', [ParameterController::class, 'updateShift']);
    Route::post('/parameters/shifts/delete/{id}', [ParameterController::class, 'deleteShift']);
    Route::post('/parameters/campaign-types/store', [ParameterController::class, 'storeCampaignType']);
    Route::get('/parameters/campaign-types/edit/{id}', [ParameterController::class, 'editCampaignType']);
    Route::post('/parameters/campaign-types/update/{id}', [ParameterController::class, 'updateCampaignType']);
    Route::post('/parameters/campaign-types/delete/{id}', [ParameterController::class, 'deleteCampaignType']);
    Route::get('/volunteers', [VolunteerController::class, 'index']);
    Route::get('/volunteers/view/{id}', [VolunteerController::class, 'view']);
    Route::post('/volunteers/skills/store', [VolunteerController::class, 'storeSkill']);
    Route::post('/volunteers/skills/delete/{id}', [VolunteerController::class, 'deleteSkill']);
    Route::get('/volunteers/edit/{id}', [VolunteerController::class, 'edit']);
    Route::post('/volunteers/update/{id}', [VolunteerController::class, 'update']);
    Route::post('/volunteers/assignment/store', [VolunteerController::class, 'storeAssignment']);
    Route::get('/volunteers/assignment/delete/{id}', [VolunteerController::class, 'deleteAssignment']);
    Route::get('/donation', [DonationController::class, 'index']);
    Route::get('/donation/create', [DonationController::class, 'create']);
    Route::post('/donation/store', [DonationController::class, 'store']);
    Route::get('/donation/edit/{id}', [DonationController::class, 'edit']);
    Route::post('/donation/update/{id}', [DonationController::class, 'update']);
    Route::get('/donation/view/{id}', [DonationController::class, 'show']);
    Route::get('/donation/delete/{id}', [DonationController::class, 'delete']);
    Route::get('/donation/allocation-details', [DonationController::class, 'allocationDetails']);
    Route::get('/donation/export', [DonationController::class, 'exportPdf']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::get('/communication', [CommunicationController::class, 'index']);
    Route::get('/communication/create', [CommunicationController::class, 'create']);
    Route::post('/communication/store', [CommunicationController::class, 'store']);
    Route::get('/communication/view/{id}', [CommunicationController::class, 'show']);
    Route::get('/communication/delete/{id}', [CommunicationController::class, 'delete']);
    Route::get('/communication/send/{id}', [CommunicationController::class, 'send']);
});

Route::middleware('role:donor')->group(function () {

    Route::get('/donor/dashboard', function () {

        $campaigns = Campaign::where(
            'status',
            'Approved'
        )->get()->toArray();

        $history = App\Models\Donation::where(
            'user_id',
            session('user.id')
        )
            ->latest()
            ->take(5)
            ->get();

        foreach ($campaigns as &$campaign) {

            $campaign['collected'] = Donation::where(
                'campaign_id',
                $campaign['id']
            )
                ->where(
                    'status',
                    'Allocated'
                )
                ->sum('amount');

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
        $communityFund = App\Models\Donation::where(
            'campaign_type',
            'Community Fund'
        )
            ->where(
                'status',
                'Allocated'
            )
            ->sum('amount');
        $campaignsSupported = App\Models\Donation::where(
            'user_id',
            session('user.id')
        )
            ->where(
                'status',
                'Allocated'
            )
            ->whereNotNull(
                'campaign_id'
            )
            ->distinct()
            ->count('campaign_id');

        $totalContributed = App\Models\Donation::where(
            'user_id',
            session('user.id')
        )
            ->where(
                'status',
                'Allocated'
            )
            ->sum('amount');

        return view(
            'donor.dashboard',
            compact(
                'campaigns',
                'history',
                'communityFund',
                'totalContributed',
                'campaignsSupported'
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

            $campaign = Campaign::where(
                'name',
                $request->campaign_name
            )->first();

            session([

                'transaction_counter' =>
                    $nextReference,

                'payment_amount' =>
                    $request->input('amount'),

                'payment_method' =>
                    $method,

                'payment_reference' =>
                    'SCC-' .
                    now()->format('Ymd') .
                    '-' .
                    $nextReference,

                'campaign_id' =>
                    $campaign?->id,

                'campaign_name' =>
                    $campaign?->name
                    ?? 'Community Fund',

            ]);

            return view(
                'donor.payment.gateway'
            );

        }
    );

    Route::get(
        '/donor/payment-confirmation',
        function () {

            $donation =
                Donation::findOrFail(
                    session('latest_donation_id')
                );

            return view(
                'donor.payment.payment-confirmation',
                compact('donation')
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

            $donation = Donation::create([

                'user_id' =>
                    session('user.id'),

                'campaign_id' =>
                    session('campaign_id'),

                'contributor' =>
                    session('user.name'),

                'transaction_id' =>
                    session('payment_reference'),

                'campaign_type' =>
                    session('campaign_id')
                    ? Campaign::find(session('campaign_id'))?->type
                    : 'Community Fund',

                'status' =>
                    'Allocated',

                'amount' =>
                    session('payment_amount'),

                'payment_method' =>
                    session('payment_method'),

                'receipt_no' =>
                    'RCP-' . now()->timestamp

            ]);

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
                'latest_donation_id' => $donation->id
            ]);

            return redirect(
                '/donor/payment-confirmation'
            );

        }
    );

    Route::post(
        '/donor/payment/fail',
        function () {

            $donation = Donation::create([

                'user_id' =>
                    session('user.id'),

                'campaign_id' =>
                    session('campaign_id'),

                'contributor' =>
                    session('user.name'),

                'transaction_id' =>
                    session('payment_reference'),

                'campaign_type' =>
                    session('campaign_id')
                    ? Campaign::find(session('campaign_id'))?->type
                    : 'Community Fund',

                'status' =>
                    'Failed',

                'amount' =>
                    session('payment_amount'),

                'payment_method' =>
                    session('payment_method'),

                'receipt_no' =>
                    'RCP-' . now()->timestamp

            ]);

            session([
                'latest_donation_id' =>
                    $donation->id
            ]);

            return redirect(
                '/donor/payment-confirmation'
            );

        }
    );

    Route::get(
        '/donor/download-receipt/{id}',
        function ($id) {

            $donation = App\Models\Donation::findOrFail($id);

            $data = [

                'campaign' =>
                    $donation->campaign_type,

                'amount' =>
                    $donation->amount,

                'method' =>
                    $donation->payment_method,

                'reference' =>
                    $donation->transaction_id,

                'datetime' =>
                    $donation->created_at->timezone('Asia/Kuala_Lumpur'),

                'status' =>
                    $donation->status

            ];

            $pdf = Pdf::loadView(
                'donor.receipts.pdf',
                $data
            );

            return $pdf->download(
                'Receipt-' .
                $donation->transaction_id .
                '.pdf'
            );

        }
    );

    Route::get('/donor/donation', function () {

        $campaigns = Campaign::where(
            'status',
            'Approved'
        )->get();

        return view(
            'donor.donation.index',
            compact('campaigns')
        );

    });

    Route::get(
        '/donor/history',
        function (\Illuminate\Http\Request $request) {

            $status = $request->status;
            $search = $request->search;

            $history = Donation::where(
                'user_id',
                session('user.id')
            );

            if (!empty($status)) {

                $history->where(
                    'status',
                    $status
                );
            }

            if (!empty($search)) {

                $history->where(function ($query) use ($search) {

                    $query->where(
                        'transaction_id',
                        'LIKE',
                        "%{$search}%"
                    )

                        ->orWhere(
                            'campaign_type',
                            'LIKE',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'payment_method',
                            'LIKE',
                            "%{$search}%"
                        );

                });
            }

            $history = $history
                ->latest()
                ->paginate(8)
                ->withQueryString();

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
            $transaction =
                Donation::findOrFail($id);

            session([

                'payment_reference' =>
                    $transaction->transaction_id,

                'payment_amount' =>
                    $transaction->amount,

                'payment_method' =>
                    $transaction->payment_method,

                'payment_status' =>
                    $transaction->status,

                'payment_datetime' =>
                    $transaction->created_at,

                'campaign_name' =>
                    $transaction->campaign_type,

            ]);

            return redirect(
                '/donor/payment-confirmation'
            );

        }
    );
    Route::get(
        '/donor/notifications',
        function () {

            $notifications =
                App\Models\UserNotification::with(
                    'notification'
                )
                    ->where(
                        'user_id',
                        session('user.id')
                    )
                    ->latest()
                    ->get();

            return view(
                'donor.notifications',
                compact(
                    'notifications'
                )
            );

        }
    );

    Route::post(
        '/donor/notifications/{id}/read',
        function ($id) {

            \App\Models\UserNotification::where(
                'id',
                $id
            )->update([
                        'is_read' => true
                    ]);

            return response()->json([
                'success' => true
            ]);
        }
    );

});

///////////////// VOLUNTEER PORTAL ROUTES
Route::middleware('role:volunteer')->group(function () {
    Route::get(
        '/volunteer/dashboard',
        [VolunteerPortalController::class, 'dashboard']
    )->name('volunteer.dashboard');

    Route::get(
        '/volunteer/history',
        [VolunteerPortalController::class, 'history']
    )->name('volunteer.history');

    Route::get(
        '/volunteer/attendance',
        [VolunteerPortalController::class, 'attendance']
    )->name('volunteer.attendance');


    Route::get(
        '/volunteer/applications',
        [VolunteerPortalController::class, 'applications']
    )->name('volunteer.applications');

    Route::post(
        '/volunteer/apply',
        [VolunteerPortalController::class, 'apply']
    )
        ->name('volunteer.apply');

    Route::get(
        '/volunteer/application-success',
        [VolunteerPortalController::class, 'applicationSuccess']
    )->name('volunteer.application.success');

    Route::get(
        '/volunteer/application/{id}',
        [VolunteerPortalController::class, 'application']
    )->name('volunteer.application');

    Route::get(
        '/volunteer/application/view/{id}',
        [VolunteerPortalController::class, 'viewApplication']
    )->name('volunteer.application.view');

    Route::get(
        '/volunteer/application/withdraw/{id}',
        [VolunteerPortalController::class, 'withdraw']
    )->name('volunteer.withdraw');

    Route::get(
        '/volunteer/assignments',
        [VolunteerPortalController::class, 'assignments']
    )->name('volunteer.assignments');

    Route::get(
        '/volunteer/notifications',
        function () {

            return view(
                'volunteer.notifications'
            );

        }
    );

});