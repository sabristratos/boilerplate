<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <h2 class="text-center text-xl font-bold text-gray-900 mb-6">Two Factor Authentication</h2>

        @if ($error)
            <div class="mb-4 p-4 bg-red-50 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (!$usingRecoveryCode)
            <div>
                <p class="text-sm text-gray-600 mb-4">
                    Please confirm access to your account by entering the authentication code provided by your authenticator application.
                </p>

                <form wire:submit="verifyCode">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                        <div class="mt-1">
                            <input wire:model="code" id="code" type="text" inputmode="numeric" autocomplete="one-time-code" required autofocus class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <button type="button" wire:click="toggleRecoveryCode" class="text-sm text-indigo-600 hover:text-indigo-500">
                            Use a recovery code
                        </button>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition">
                            Verify
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div>
                <p class="text-sm text-gray-600 mb-4">
                    Please confirm access to your account by entering one of your emergency recovery codes.
                </p>

                <form wire:submit="verifyCode">
                    <div>
                        <label for="recovery-code" class="block text-sm font-medium text-gray-700">Recovery Code</label>
                        <div class="mt-1">
                            <input wire:model="recoveryCode" id="recovery-code" type="text" autocomplete="off" required autofocus class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <button type="button" wire:click="toggleRecoveryCode" class="text-sm text-indigo-600 hover:text-indigo-500">
                            Use an authentication code
                        </button>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition">
                            Verify
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
