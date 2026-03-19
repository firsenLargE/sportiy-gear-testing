<?php

namespace App\Filament\Resources\Discounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\ProductVariant;

class DiscountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Discount Information')
                ->schema([

                    TextInput::make('name')
                        ->required()
                        ->string()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),

                    Select::make('discount_type')
                        ->options([
                            'percentage' => 'Percentage',
                            'fixed' => 'Fixed Amount',
                        ])
                        ->required()
                        ->native(false)
                        ->rules([
                            Rule::in(['percentage', 'fixed'])
                        ]),

                    TextInput::make('discount_value')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->rules(['numeric', 'min:0']),

                    DatePicker::make('start_date')
                        ->required()
                        ->reactive()
                        ->minDate(today())
                        ->rule('after_or_equal:today')
                        ->helperText('Start date cannot be before today'),

                    DatePicker::make('end_date')
                        ->minDate(fn($get) => $get('start_date'))
                        ->rule('after_or_equal:start_date')
                        ->helperText('End date must be after or equal to start date'),

                ]),

            Section::make('Apply Discount To Variants (Optional)')
                ->schema([

                    Toggle::make('select_all_variants')
                        ->label('Apply to All Variants')
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {

                            if ($state) {
                                $set(
                                    'productVariants',
                                    ProductVariant::pluck('id')->toArray()
                                );
                            } else {
                                $set('productVariants', []);
                            }
                        }),

                    Select::make('productVariants')
                        ->label('Product Variants')
                        ->relationship('productVariants', 'sku')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->rules(['array'])
                        ->options(function () {

                            return Product::with('variants')
                                ->get()
                                ->mapWithKeys(function ($product) {

                                    return [
                                        $product->name => $product->variants
                                            ->mapWithKeys(function ($variant) {
                                                return [
                                                    $variant->id => $variant->sku
                                                ];
                                            })
                                            ->toArray()
                                    ];
                                })
                                ->toArray();
                        }),

                ]),
        ]);
    }
}
