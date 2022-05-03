<x-hub::input.group label="Display Name" for="name" :error="$errors->first('shippingMethod.name')">
  <x-hub::input.text wire:model="shippingMethod.name" name="name" id="name" :error="$errors->first('shippingMethod.name')" />
</x-hub::input.group>

<x-hub::input.group label="Description" for="description" :error="$errors->first('shippingMethod.description')">
  <x-hub::input.textarea wire:model="shippingMethod.description" name="description" id="description" :error="$errors->first('shippingMethod.description')" />
</x-hub::input.group>

<x-hub::input.group label="Code" for="code" :error="$errors->first('shippingMethod.code')">
  <x-hub::input.text wire:model="shippingMethod.code" name="code" id="code" :error="$errors->first('shippingMethod.code')" />
</x-hub::input.group>
