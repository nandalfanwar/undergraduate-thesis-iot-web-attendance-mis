<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

<!-- customized update password -->
<!-- <section>

    <header class="mb-4">

        <h5 class="fw-bold mb-1">
            Keamanan Akun
        </h5>

        <p class="text-muted small mb-0">
            Gunakan password yang kuat untuk menjaga keamanan akun.
        </p>

    </header>



    <form method="post" action="{{ route('password.update') }}">

        @csrf
        @method('put')



        {{-- Password Lama --}}
        <div class="mb-3">

            <label class="form-label">
                Password Lama
            </label>

            <input
                type="password"
                name="current_password"
                class="form-control">

            @error('current_password')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror

        </div>



        {{-- Password Baru --}}
        <div class="mb-3">

            <label class="form-label">
                Password Baru
            </label>

            <input
                type="password"
                name="password"
                class="form-control">

            @error('password')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror

        </div>



        {{-- Konfirmasi --}}
        <div class="mb-4">

            <label class="form-label">
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="password_confirmation"
                class="form-control">

        </div>



        <button class="btn btn-success px-4">

            <i class="bi bi-key me-1"></i>
            Ubah Password

        </button>


    </form>

</section> -->