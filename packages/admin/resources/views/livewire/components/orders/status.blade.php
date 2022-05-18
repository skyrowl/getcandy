<div>
  <button
    class="inline-flex items-center px-4 py-2 font-bold transition border border-transparent border-gray-200 rounded hover:bg-white bg-gray-50 hover:border-gray-200"
    type="button"
    wire:click.prevent="$set('visible', true)"
  >
    Update Status
  </button>
  <x-hub::modal.dialog wire:model="visible" form="updateStatus">
    <x-slot name="title">
      {{ __('adminhub::orders.update_status.title') }}
    </x-slot>
    <x-slot name="content">
      <x-hub::input.group :label="__('adminhub::inputs.status.label')" for="status" required :error="$errors->first('status')">
        <x-hub::input.select wire:model.defer="order.status" required>
          @foreach($this->statuses as $handle => $status)
            <option value="{{ $handle }}">{{ $status['label'] }}</option>
          @endforeach
        </x-hub::input.select>
      </x-hub::input.group>
    </x-slot>
    <x-slot name="footer">
      <x-hub::button type="button" wire:click.prevent="$set('visible', false)" theme="gray">{{ __('adminhub::global.cancel') }}</x-hub::button>
      <x-hub::button type="submit">
        {{ __('adminhub::orders.update_status.btn') }}
      </x-hub::button>
    </x-slot>
  </x-hub::modal>
</div>
