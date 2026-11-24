@extends('layouts.organizer')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div><h1 class="text-2xl font-bold text-slate-900">Register New Volunteer</h1><p class="mt-1 text-sm text-slate-500">Create a volunteer account using the platform's volunteer database.</p></div>
        <a href="/volunteers" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Back</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><p class="mb-2 font-semibold">Please correct the following:</p><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="/volunteers/store" class="space-y-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <section>
            <h2 class="mb-4 text-lg font-semibold text-slate-800">Personal Information</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium text-slate-700">Full Name <span class="text-red-500">*</span><input name="name" value="{{ old('name') }}" required maxlength="255" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" /></label>
                <label class="text-sm font-medium text-slate-700">Email Address <span class="text-red-500">*</span><input type="email" name="email" value="{{ old('email') }}" required maxlength="255" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" /></label>
                <label class="text-sm font-medium text-slate-700">Phone Number<input name="phone" value="{{ old('phone') }}" maxlength="30" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" /></label>
                <label class="text-sm font-medium text-slate-700">Age<input type="number" name="age" value="{{ old('age') }}" min="13" max="120" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" /></label>
                <label class="text-sm font-medium text-slate-700">Account Status <span class="text-red-500">*</span><select name="volunteer_status" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"><option value="Active" @selected(old('volunteer_status', 'Active') === 'Active')>Active</option><option value="Inactive" @selected(old('volunteer_status') === 'Inactive')>Inactive</option></select></label>
            </div>
        </section>

        <section>
            <h2 class="mb-1 text-lg font-semibold text-slate-800">Volunteer Account Password</h2>
            <p class="mb-4 text-xs text-slate-500">Set a temporary password for the new volunteer. It must contain at least eight characters, one uppercase letter and one number.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium text-slate-700">Password <span class="text-red-500">*</span><input type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" /></label>
                <label class="text-sm font-medium text-slate-700">Confirm Password <span class="text-red-500">*</span><input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" /></label>
            </div>
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold text-slate-800">Volunteer Skills</h2>
            @if($skills->isEmpty())
                <p class="rounded-lg bg-amber-50 p-3 text-sm text-amber-800">No active skills are configured. You can add skills later through System Admin parameters.</p>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($skills as $skill)
                        <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm text-slate-700"><input type="checkbox" name="skills[]" value="{{ $skill->id }}" @checked(in_array($skill->id, old('skills', []))) class="rounded border-slate-300 text-red-600 focus:ring-red-500">{{ $skill->name }}</label>
                    @endforeach
                </div>
            @endif
        </section>

        <section>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Additional Notes</label>
            <textarea name="notes" rows="4" maxlength="3000" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Volunteer background, availability or other relevant remarks">{{ old('notes') }}</textarea>
        </section>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="/volunteers" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-600">Cancel</a>
            <button type="submit" class="rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700">Register Volunteer</button>
        </div>
    </form>
</div>
@endsection
