<?php

namespace App\Filament\Tenant\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class RestaurantSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.tenant.pages.restaurant-settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    protected static ?string $title = 'General Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = [
            'restaurant_name' => Setting::get('restaurant_name', 'My Restaurant'),
            'restaurant_phone' => Setting::get('restaurant_phone', ''),
            'restaurant_address' => Setting::get('restaurant_address', ''),
            'tax_rate' => Setting::get('tax_rate', '5'),
            'currency' => Setting::get('currency', 'USD'),
            'logo' => Setting::get('logo', ''),
            'primary_color' => Setting::get('primary_color', '#4f46e5'),
        ];

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Restaurant Information')
                    ->description('Update your restaurant details here.')
                    ->schema([
                        TextInput::make('restaurant_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('restaurant_phone')
                            ->tel()
                            ->maxLength(255),
                        Textarea::make('restaurant_address')
                            ->columnSpanFull(),
                        FileUpload::make('logo')
                            ->image()
                            ->directory(fn () => 'tenants/' . tenant('id') . '/settings')
                            ->columnSpanFull(),
                        \Filament\Forms\Components\ColorPicker::make('primary_color')
                            ->label('Brand Primary Color')
                            ->required(),
                    ])->columns(2),
                
                Section::make('Financial Settings')
                    ->description('Set tax rates and currency.')
                    ->schema([
                        TextInput::make('tax_rate')
                            ->numeric()
                            ->suffix('%')
                            ->label('Default Tax Rate')
                            ->required(),
                        \Filament\Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'US Dollar ($)',
                                'BDT' => 'Bangladeshi Taka (৳)',
                                'EUR' => 'Euro (€)',
                                'GBP' => 'British Pound (£)',
                                'INR' => 'Indian Rupee (₹)',
                            ])
                            ->searchable()
                            ->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->success()
            ->title('Settings Saved')
            ->body('Your restaurant settings have been updated successfully.')
            ->send();
    }
}
