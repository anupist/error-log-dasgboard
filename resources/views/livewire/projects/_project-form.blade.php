{{-- Shared form partial used by both Create and Edit modals --}}
{{-- $isEdit is optional, defaults to false --}}
@php $isEdit = $isEdit ?? false; @endphp

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Project Name <span class="text-red-500">*</span>
    </label>
    <input wire:model.live="form_name" type="text" placeholder="e.g. Ecommerce Production"
           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
    @error('form_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Slug <span class="text-red-500">*</span>
    </label>
    <input wire:model="form_slug" type="text" placeholder="ecommerce-production"
           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white font-mono">
    @error('form_slug') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        API Base URL <span class="text-red-500">*</span>
    </label>
    <input wire:model="form_api_url" type="url" placeholder="https://your-app.com/public-api/log-errors"
           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
    @error('form_api_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        API Token
        @if($isEdit)
            <span class="text-xs text-gray-400 font-normal">(leave blank to keep existing)</span>
        @else
            <span class="text-xs text-gray-400 font-normal">(optional)</span>
        @endif
    </label>
    <input wire:model="form_api_token" type="password" placeholder="Bearer token…"
           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
    @error('form_api_token') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Environment</label>
        <select wire:model="form_environment"
                class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
            <option value="production">Production</option>
            <option value="staging">Staging</option>
            <option value="development">Development</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
        <select wire:model="form_status"
                class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
    <textarea wire:model="form_notes" rows="2" placeholder="Optional notes about this project…"
              class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white resize-none"></textarea>
    @error('form_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>
