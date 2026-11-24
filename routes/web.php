<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrganizerDashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VolunteerPortalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DonorController;

Route::view('/', 'welcome');

Route::get('/auth', function () {
    return view('auth');
})->name('auth');

Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Forgot Password routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:5,1')
    ->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
    ->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('throttle:5,1')
    ->name('password.update');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// System Administrator routes
Route::middleware('role:administrator')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Shared system parameters: managed by System Administrator only.
    Route::get('/parameters', [ParameterController::class, 'index'])->name('parameters.index');
    Route::post('/parameters/skills/store', [ParameterController::class, 'storeSkill'])->name('parameters.skills.store');
    Route::get('/parameters/skills/edit/{id}', [ParameterController::class, 'editSkill'])->name('parameters.skills.edit');
    Route::post('/parameters/skills/update/{id}', [ParameterController::class, 'updateSkill'])->name('parameters.skills.update');
    Route::post('/parameters/skills/deactivate/{id}', [ParameterController::class, 'deleteSkill'])->name('parameters.skills.deactivate');

    Route::post('/parameters/roles/store', [ParameterController::class, 'storeRole'])->name('parameters.roles.store');
    Route::get('/parameters/roles/edit/{id}', [ParameterController::class, 'editRole'])->name('parameters.roles.edit');
    Route::post('/parameters/roles/update/{id}', [ParameterController::class, 'updateRole'])->name('parameters.roles.update');
    Route::post('/parameters/roles/deactivate/{id}', [ParameterController::class, 'deleteRole'])->name('parameters.roles.deactivate');

    Route::post('/parameters/shifts/store', [ParameterController::class, 'storeShift'])->name('parameters.shifts.store');
    Route::get('/parameters/shifts/edit/{id}', [ParameterController::class, 'editShift'])->name('parameters.shifts.edit');
    Route::post('/parameters/shifts/update/{id}', [ParameterController::class, 'updateShift'])->name('parameters.shifts.update');
    Route::post('/parameters/shifts/deactivate/{id}', [ParameterController::class, 'deleteShift'])->name('parameters.shifts.deactivate');

    Route::post('/parameters/campaign-types/store', [ParameterController::class, 'storeCampaignType'])->name('parameters.campaign-types.store');
    Route::get('/parameters/campaign-types/edit/{id}', [ParameterController::class, 'editCampaignType'])->name('parameters.campaign-types.edit');
    Route::post('/parameters/campaign-types/update/{id}', [ParameterController::class, 'updateCampaignType'])->name('parameters.campaign-types.update');
    Route::post('/parameters/campaign-types/deactivate/{id}', [ParameterController::class, 'deleteCampaignType'])->name('parameters.campaign-types.deactivate');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/update-role/{id}', [AdminController::class, 'updateUserRole'])->whereNumber('id')->name('users.update-role');

    Route::get('/organizations', [AdminController::class, 'organizations'])->name('organizations');
    Route::post('/organizations/verify/{id}', [AdminController::class, 'verifyOrganization'])->whereNumber('id')->name('organizations.verify');

    Route::get('/logs', [AdminController::class, 'activityLogs'])->name('logs');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/notifications', [AdminController::class, 'broadcast'])->name('notifications.create');
    Route::post('/notifications/send', [AdminController::class, 'sendBroadcast'])->name('notifications.send');
});


Route::get('/profile', [ProfileController::class, 'index']);
Route::get('/profile/edit', [ProfileController::class, 'edit']);
Route::post('/profile/update', [ProfileController::class, 'update']);
Route::get('/profile/password', [ProfileController::class, 'password']);
Route::post('/profile/password', [ProfileController::class, 'changePassword']);

//// Organizer Portal Routes
Route::middleware('role:organizer')->group(function () {
    Route::get('/dashboard', [OrganizerDashboardController::class, 'index'])
        ->name('organizer.dashboard');
    Route::get('/campaign', [CampaignController::class, 'index']);
    Route::post('/campaign/store', [CampaignController::class, 'store']);
    Route::get('/campaign/create', [CampaignController::class, 'create']);
    Route::get('/campaign/edit/{id}', [CampaignController::class, 'edit']);
    Route::post('/campaign/update/{id}', [CampaignController::class, 'update']);
    Route::post('/campaign/delete/{id}', [CampaignController::class, 'delete']);
    Route::get('/campaign/recruiting/{id}', [CampaignController::class, 'startRecruitment']);
    Route::get('/campaign/start/{id}', [CampaignController::class, 'startCampaign']);
    Route::get('/campaign/complete/{id}', [CampaignController::class, 'completeCampaign']);
    Route::get(
        '/campaign/attendance/{id}',
        [CampaignController::class, 'attendance']
    );

    Route::post(
        '/campaign/attendance/store/{id}',
        [CampaignController::class, 'storeAttendance']
    );
    Route::get('/organizer/campaigns/{campaign}/applications', [CampaignController::class, 'applications'])->name('organizer.campaigns.applications');
    Route::post('/applications/{application}/approve', [CampaignController::class, 'approveApplication'])->name('applications.approve');
    Route::post(
        '/applications/{application}/reject',
        [CampaignController::class, 'rejectApplication']
    )->name('applications.reject');
    Route::get('/volunteers', [VolunteerController::class, 'index']);
    Route::get('/volunteers/create', [VolunteerController::class, 'create']);
    Route::post('/volunteers/store', [VolunteerController::class, 'store']);
    Route::get('/volunteers/applications', [VolunteerController::class, 'applications']);
    Route::get('/volunteers/view/{id}', [VolunteerController::class, 'view']);
    Route::get('/volunteers/edit/{id}', [VolunteerController::class, 'edit']);
    Route::post('/volunteers/update/{id}', [VolunteerController::class, 'update']);
    Route::post('/volunteers/status/{id}', [VolunteerController::class, 'updateStatus']);
    Route::post('/volunteers/delete/{id}', [VolunteerController::class, 'delete']);
    Route::post('/volunteers/skills/store', [VolunteerController::class, 'storeSkill']);
    Route::post('/volunteers/skills/delete/{id}', [VolunteerController::class, 'deleteSkill']);
    Route::post('/volunteers/assignment/store', [VolunteerController::class, 'storeAssignment']);
    Route::post('/volunteers/assignment/update/{id}', [VolunteerController::class, 'updateAssignment']);
    Route::post('/volunteers/assignment/delete/{id}', [VolunteerController::class, 'deleteAssignment']);
    Route::get('/donation', [DonationController::class, 'index'])->name('organizer.donation.index');
    Route::get('/donation/view/{id}', [DonationController::class, 'show'])->whereNumber('id')->name('organizer.donation.view');
    Route::get('/donation/allocation-details', [DonationController::class, 'allocationDetails'])->name('organizer.donation.allocations');
    Route::get('/donation/export', [DonationController::class, 'exportPdf'])->name('organizer.donation.export');
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::get('/communication/view/{id}', [CommunicationController::class, 'show'])
        ->name('communication.show');
    Route::get('/communication', [CommunicationController::class, 'index'])->name('communication.index');
    Route::get('/communication/create', [CommunicationController::class, 'create'])->name('communication.create');
    Route::post('/communication/store', [CommunicationController::class, 'store'])->name('communication.store');

});

Route::middleware('role:donor')->prefix('donor')->name('donor.')->group(function () {
    Route::get('/dashboard', [DonorController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('/donation', [DonorController::class, 'campaignDonation'])
        ->name('donation');
    Route::get('/fund', [DonorController::class, 'generalFund'])
        ->name('fund');

    Route::post('/payment/start', [DonorController::class, 'startPayment'])
        ->name('payment.start');
    Route::get('/payment/gateway/{id}', [DonorController::class, 'paymentGateway'])
        ->whereNumber('id')->name('payment.gateway');
    Route::post('/payment/success/{id}', [DonorController::class, 'paymentSuccess'])
        ->whereNumber('id')->name('payment.success');
    Route::post('/payment/failed/{id}', [DonorController::class, 'paymentFailed'])
        ->whereNumber('id')->name('payment.failed');
    Route::get('/payment/confirmation/{id}', [DonorController::class, 'paymentConfirmation'])
        ->whereNumber('id')->name('payment.confirmation');

    Route::get('/history', [DonorController::class, 'history'])
        ->name('history');
    Route::get('/history/{id}', [DonorController::class, 'historyDetails'])
        ->whereNumber('id')->name('history.details');
    Route::get('/receipt/{id}', [DonorController::class, 'downloadReceipt'])
        ->whereNumber('id')->name('receipt');

    Route::get('/notifications', [DonorController::class, 'notifications'])
        ->name('notifications');
    Route::post('/notifications/{id}/read', [DonorController::class, 'markNotificationRead'])
        ->whereNumber('id')->name('notifications.read');
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
        [NotificationController::class, 'volunteerIndex']
    )->name('volunteer.notifications');

    Route::post(
        '/volunteer/notifications/{id}/read',
        [NotificationController::class, 'markAsRead']
    )->name('volunteer.notifications.read');

});
