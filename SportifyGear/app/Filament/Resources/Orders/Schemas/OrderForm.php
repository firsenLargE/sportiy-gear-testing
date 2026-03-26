<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use App\Models\ProductVariant;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([


                Section::make('Order Details')
                    ->columns(1)
                    ->schema([

                        Grid::make(1)->schema([

                            Select::make('user_id')
                                ->label('Customer')
                                ->relationship('user', 'name')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? 'No Name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('status_id')
                                ->label('Order Status')
                                ->relationship('status', 'name')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? 'No Status')
                                ->required(),

                            Select::make('shipping_method_id')
                                ->label('Shipping Method')
                                ->relationship('shippingMethod', 'name')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? 'No Shipping')
                                ->required(),

                            Select::make('coupon_id')
                                ->label('Coupon')
                                ->relationship('coupon', 'code')
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->code ?? 'No Code')
                                ->searchable()
                                ->nullable(),

                            TextInput::make('order_number')
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('sub_total')
                                ->numeric()
                                ->prefix('Rs')
                                ->disabled()
                                ->dehydrated(),

                            TextInput::make('total')
                                ->numeric()
                                ->prefix('Rs')
                                ->disabled()
                                ->dehydrated(),
                        ]),
                    ])
                    ->collapsible(),


                Section::make('Order Items')
                    ->columns(1)
                    ->schema([

                        Repeater::make('items')
                            ->relationship()
                            ->live()
                            ->schema([

                                Grid::make(1)->schema([

                                    Select::make('product_id')
                                        ->label('Product')
                                        ->relationship('product', 'name')
                                        ->getOptionLabelFromRecordUsing(fn($record) => $record->name ?? 'Unnamed Product')
                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->required()
                                        ->afterStateUpdated(fn($set) => $set('product_variant_id', null)),

                                    Select::make('product_variant_id')
                                        ->label('Variant')
                                        ->options(function ($get) {
                                            $productId = $get('product_id');

                                            if (!$productId) return [];

                                            return ProductVariant::where('product_id', $productId)
                                                ->get()
                                                ->mapWithKeys(fn($variant) => [
                                                    $variant->id => $variant->name ?? 'Unnamed Variant'
                                                ]);
                                        })
                                        ->searchable()
                                        ->reactive()
                                        ->required()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state) {
                                                $variant = ProductVariant::find($state);
                                                $set('price', $variant?->price ?? 0);
                                            }
                                        }),

                                    TextInput::make('quantity')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->reactive()
                                        ->required(),

                                    TextInput::make('price')
                                        ->numeric()
                                        ->prefix('Rs')
                                        ->reactive()
                                        ->required(),
                                ]),
                            ])

                            ->columns(1)
                            ->collapsible()
                            ->cloneable()
                            ->reorderable()
                            ->addActionLabel('➕ Add Product')

                            ->afterStateUpdated(function ($state, callable $set) {
                                $subTotal = collect($state)->sum(function ($item) {
                                    return ($item['quantity'] ?? 0) * ($item['price'] ?? 0);
                                });

                                $set('sub_total', $subTotal);
                                $set('total', $subTotal);
                            }),

                    ])
                    ->collapsible(),
            ]);
    }
}
