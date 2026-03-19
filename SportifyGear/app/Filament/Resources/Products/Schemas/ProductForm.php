<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            //category Information
            Section::make('Categories')
                ->description('Assign product to categories')
                ->icon('heroicon-o-folder')
                ->columns(1)
                ->schema([

                    Select::make('categories')
                        ->label('Categories')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required()
                        ->minItems(1)
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('New Category Name')
                                ->required()
                                ->string()
                                ->maxLength(255)
                                ->unique(Category::class, 'name')
                                ->reactive()
                                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state)))
                                ->columnSpanFull(),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(Category::class, 'slug')
                                ->helperText('Automatically generated from name')
                                ->columnSpanFull(),
                            Select::make('parent_id')
                                ->label('Parent Category')
                                ->relationship('parent', 'name')
                                ->nullable()
                                ->placeholder('None (Top Level)')
                                ->searchable()
                                ->preload()
                                ->columnSpanFull(),
                        ])
                        ->createOptionUsing(fn(array $data) => Category::create($data)->id)
                        ->createOptionAction(fn($action) => $action->label('Add New Category'))
                        ->helperText('Select one or more categories for this product')
                        ->columnSpanFull(),

                ])
                ->columnSpanFull()
                ->collapsible(),

            //Product Information 
            Section::make('Product Information')
                ->description('Basic information about the product')
                ->icon('heroicon-o-cube')
                ->columns(2)
                ->schema([

                    TextInput::make('name')
                        ->label('Product Name')
                        ->required()
                        ->reactive()
                        ->minLength(3)
                        ->maxLength(255)
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            $currentSlug = $get('slug');
                            $autoSlug = Str::slug($state);
                            if (empty($currentSlug) || $currentSlug === $autoSlug) {
                                $set('slug', $autoSlug);
                            }
                        }),

                    TextInput::make('slug')
                        ->label('Product Slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Product::class, 'slug', ignoreRecord: true)
                        ->reactive(),

                    RichEditor::make('description')
                        ->placeholder('Write product description...')
                        ->maxLength(5000)
                        ->columnSpanFull(),

                    TextInput::make('meta_title')
                        ->placeholder('SEO title')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('meta_description')
                        ->placeholder('SEO description')
                        ->maxLength(500)
                        ->columnSpanFull(),

                    TextInput::make('admin_name')
                        ->label('Created by')
                        ->disabled()
                        ->formatStateUsing(fn(?string $state, $record) => $record?->admin?->name ?? auth('admin')->user()?->name ?? '—')
                        ->columnSpanFull(),

                    Hidden::make('admin_id')
                        ->required()
                        ->default(fn() => auth('admin')->id()),

                    Toggle::make('is_active')
                        ->label('Product Status')
                        ->required()
                        ->helperText('Enable or disable product visibility')
                        ->columnSpanFull(),

                ])
                ->columnSpanFull()
                ->collapsible(),

            //Product Images
            Section::make('Product Images')
                ->description('Upload product images')
                ->icon('heroicon-o-photo')
                ->columnSpanFull()
                ->schema([

                    Repeater::make('images')
                        ->relationship('images')
                        ->collapsed()
                        ->minItems(1)
                        ->schema([

                            FileUpload::make('image_path')
                                ->image()
                                ->label('Image Upload')
                                ->imagePreviewHeight('200')
                                ->directory('products')
                                ->required()
                                ->maxSize(1024)
                                ->columnSpanFull(),

                            Select::make('is_primary')
                                ->label('Image Type')
                                ->options([1 => 'Primary Image', 0 => 'Secondary Image'])
                                ->default(1)
                                ->required()
                                ->columnSpanFull(),

                        ])
                        ->columnSpanFull()
                        ->addActionLabel('Add Product Image')
                        ->reorderable(),

                ])
                ->collapsible(),
            //Product Variants
            Section::make('Product Variants')
                ->description('Manage product variations like size, color etc.')
                ->icon('heroicon-o-squares-2x2')
                ->columnSpanFull()
                ->schema([

                    Repeater::make('variants')
                        ->relationship('variants')
                        ->collapsed()
                        ->columns(2)
                        ->itemLabel(fn(array $state): ?string => $state['name'] ?? $state['sku'] ?? 'Variant')
                        ->schema([

                            TextInput::make('name')
                                ->label('Variant Name')
                                ->maxLength(255)
                                ->placeholder('Example: Samsung S26 Ultra'),

                            TextInput::make('sku')
                                ->label('SKU')
                                ->placeholder('SKU Code')
                                ->required()
                                ->string()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true),

                            RichEditor::make('description')
                                ->label('Variant Description')
                                ->placeholder('Description for this variant')
                                ->maxLength(5000)
                                ->columnSpanFull(),

                            TextInput::make('price')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->maxValue(99999999)
                                ->prefix('Rs'),

                            TextInput::make('stock_quantity')
                                ->label('Stock')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->default(1),

                            TextInput::make('weight')
                                ->numeric()
                                ->numeric()
                                ->minValue(0)
                                ->suffix('kg'),

                            Select::make('discounts')
                                ->label('Discount')
                                ->relationship('discounts', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->getOptionLabelFromRecordUsing(
                                    fn($record) =>
                                    $record->name . ' (' .
                                        ($record->discount_type == 'percentage' ? $record->discount_value . '%' : 'Rs ' . $record->discount_value) . ')'
                                ),

                            Select::make('attributeValues')
                                ->label('Attributes')
                                ->relationship('attributeValues', 'value')
                                ->multiple()
                                ->preload()
                                ->searchable(['value', 'attribute.name'])
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->attribute->name . ' - ' . $record->value)
                                ->createOptionForm([
                                    Select::make('attribute_id')
                                        ->label('Attribute')
                                        ->relationship('attribute', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([TextInput::make('name')->required()->label('New Attribute')]),
                                    TextInput::make('value')->label('Value')->required(),
                                ])
                                ->columnSpanFull(),

                            Section::make('Variant Images')
                                ->description('Upload images specific to this variant')
                                ->columnSpanFull()
                                ->schema([
                                    Repeater::make('images')
                                        ->relationship('images')
                                        ->minItems(1)
                                        ->collapsed()
                                        ->schema([
                                            FileUpload::make('image_path')
                                                ->image()
                                                ->imagePreviewHeight('200')
                                                ->maxSize(1024)
                                                ->directory('product_variants')
                                                ->columnSpanFull()
                                                ->required(),

                                            Select::make('is_primary')
                                                ->label('Image Type')
                                                ->options([1 => 'Primary Image', 0 => 'Secondary Image'])
                                                ->default(0)
                                                ->columnSpanFull(),
                                        ])
                                        ->columnSpanFull()
                                        ->addActionLabel('Add Variant Image')
                                        ->reorderable(),
                                ])
                                ->collapsible(),

                        ])
                        ->columnSpanFull()
                        ->addActionLabel('Add New Variant')
                        ->reorderable(),

                ])
                ->collapsible(),

        ]);
    }
}
