<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Coupon Information')
                    ->description('Create or manage discount coupons')
                    ->schema([

                        TextInput::make('code')
                            ->label('Coupon Code')
                            ->required()
                            ->maxLength(50)
                            ->unique('coupons', 'code', ignoreRecord: true)
                            ->placeholder('SUMMER20')
                            ->helperText('Customers will enter this code at checkout')
                            ->live()
                            ->afterStateUpdated(fn($state, $set) => $set('code', strtoupper($state)))
                            ->columnSpan(1),

                        Select::make('type')
                            ->label('Discount Type')
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->columnSpan(1),

                        TextInput::make('value')
                            ->label('Discount Value')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(fn(Get $get) => $get('type') === 'percentage' ? 100 : null)
                            ->helperText('Example: 10 for 10% or 500 for fixed amount')
                            ->columnSpan(1),

                        TextInput::make('maximum_discount')
                            ->numeric()
                            ->minValue(1)
                            ->label('Maximum Discount')
                            ->visible(fn(Get $get) => $get('type') === 'percentage')
                            ->helperText('Limits discount when using percentage')
                            ->columnSpan(1),

                        TextInput::make('minimum_order_amount')
                            ->numeric()
                            ->minValue(1)
                            ->label('Minimum Order Amount')
                            ->placeholder('Optional')
                            ->columnSpan(1),

                        TextInput::make('usage_limit')
                            ->numeric()
                            ->minValue(1)
                            ->label('Total Usage Limit')
                            ->placeholder('Unlimited if empty')
                            ->columnSpan(1),

                        TextInput::make('usage_per_user')
                            ->numeric()
                            ->minValue(1)
                            ->label('Usage Per User')
                            ->placeholder('Unlimited if empty')
                            ->columnSpan(1),
                        DateTimePicker::make('starts_at')
                            ->label('Start Date')
                            ->required()
                            ->seconds(false)
                            ->minDate(today())
                            ->helperText('Coupon cannot start before today')
                            ->columnSpan(1),

                        DateTimePicker::make('expires_at')
                            ->label('Expiration Date')
                            ->required()
                            ->seconds(false)
                            ->minDate(fn(Get $get) => $get('starts_at') ?? today())
                            ->after('starts_at')
                            ->helperText('Expiration must be after start date')
                            ->columnSpan(1),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive coupons cannot be used')
                            ->columnSpanFull(),

                    ])

                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
