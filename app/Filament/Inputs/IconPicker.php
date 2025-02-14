<?php

namespace App\Filament\Inputs;

use App\Enums\HeroIcons;
use Filament\Forms\Components\Select;

class IconPicker extends Select
{
    public static function make(string $name): static
    {
        return parent::make($name)
            ->allowHtml()
            ->searchable()
            ->default(HeroIcons::O_RECTANGLE_STACK->value)
            ->options(function (): array {
                return collect(HeroIcons::cases())
                    ->mapWithKeys(function (HeroIcons $case) {
                        return [$case->value => "<span class='flex items-center'>
                                    " . svg($case->value, ["class" => "h-5 w-5", "style" => "margin-right: 0.4rem;"])->toHtml() . "
                                    <span>" . svg($case->value)->name() . "</span>
                                </span>"];
                    })
                    ->toArray();
            })
            ->getOptionLabelsUsing(function (mixed $value): string {
                return collect(HeroIcons::cases())
                    ->filter(fn(HeroIcons $enum) => stripos($enum->value, $value) !== false)
                    ->map(function (HeroIcons $case) {
                        return "<span class='flex items-center'>
                                    " . svg($case->value, ["class" => "h-5 w-5", "style" => "margin-right: 0.4rem;"])->toHtml() . "
                                    <span>" . svg($case->value)->name() . "</span>
                                </span>";
                    });
            })
            ->getSearchResultsUsing(function (string $search): array {
                return collect(HeroIcons::cases())
                    ->filter(fn(HeroIcons $enum) => stripos($enum->value, $search) !== false)
                    ->mapWithKeys(function (HeroIcons $case) {
                        return [$case->value => "<span class='flex items-center'>
                                    " . svg($case->value, ["class" => "h-5 w-5", "style" => "margin-right: 0.4rem;"])->toHtml() . "
                                    <span>" . svg($case->value)->name() . "</span>
                                </span>"];
                    })
                    ->toArray();
            });
    }
}
