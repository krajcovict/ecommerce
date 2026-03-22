<x-app-layout>
    <form method="POST" action="{{ route('login') }}" class="w-[400px] mx-auto p-6 my-16">
        @csrf
        <h2 class="text-2xl font-semibold text-center mb-5">
          Login to your account
        </h2>
        <p class="text-right text-gray-500 mb-6">

          <a
            href="{{ route('register') }}"
            class="text-sm text-purple-700 hover:text-purple-600"
            >{{ __('Already registered?') }}</a
          >
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-between items-center mb-5">
          <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="mr-3 rounded border-gray-300 text-purple-500 focus:ring-purple-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
          </div>
          <a href="{{ route('password.request') }}" class="text-sm text-purple-700 hover:text-purple-600">{{ __('Forgot your password?') }}</a>
        </div>
        <button
          class="btn-primary bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 w-full"
        >
          {{ __('Log in') }}
        </button>

    </form>
</x-app-layout>
