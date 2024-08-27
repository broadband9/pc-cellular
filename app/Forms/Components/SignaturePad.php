<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class SignaturePad extends Field
{
    protected string $view = 'components.signature-pad';

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (SignaturePad $component, $state) {
            // If the state is a data URL, it's already in the correct format
            if (is_string($state) && strpos($state, 'data:image/png;base64,') === 0) {
                return;
            }
            // Otherwise, you might need to convert it to a data URL
            // This depends on how you're storing the signature in the database
        });

        $this->dehydrateStateUsing(function ($state) {
            // Ensure the state is a valid data URL before saving
            if (is_string($state) && strpos($state, 'data:image/png;base64,') === 0) {
                return $state;
            }
            return null;
        });
    }
}