<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="https://experior.ai">
                <div class="h-full w-1/2 md:w-1/4 xl:w-1/5 m-auto mb-8">
                    <img src="/logo.png" class="pt-8 md:pt-0" />
                </div>
            </a>
        </x-slot>

        <div class="flex flex-col mb-6 font-courier">
            <div class="text-3xl font-bold text-zinc-700">New account</div>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label class='font-courier' for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="font-courier block mt-1 w-full" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label class='font-courier' for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="font-courier block mt-1 w-full" type="email" name="email"
                    :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-label class='font-courier' for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="font-courier block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label class='font-courier' for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="font-courier block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                <x-label for="terms">
                    <div class="flex items-center">
                        <x-checkbox name="terms" id="terms" required />

                        <div class="ml-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'"
                                class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of
                                Service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'"
                                class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy
                                Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
            </div>
            @endif

            <div class="flex items-center justify-end gap-6 mt-8">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
                <button type="submit" class="bg-secondary text-white py-2 px-4 text-center rounded-lg">Register</button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
