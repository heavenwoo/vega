<?php


namespace Vega\Twig;

use Carbon\CarbonInterface;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Carbon\Carbon;

class VegaExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('nice_number', [$this, 'niceNumberFilter']),
            new TwigFilter('ago', [$this, 'diff'])
        ];
    }

    public function niceNumberFilter($number)
    {
        if ($number > 1000000000) {
            $number = round($number / 1000000000, 1) . 'b';
        } elseif ($number > 1000000) {
            $number = round($number / 1000000, 1) . 'm';
        } elseif ($number > 1000) {
            $number = round($number / 1000, 1) . 'k';
        }

        return $number;
    }

    public function diff($since)
    {
        $since = $this->getCarbonObject($since);

        return $since->diffForHumans();
    }

    public function getCarbonObject($datetime = null)
    {
        if ($datetime instanceof CarbonInterface) {
            return $datetime;
        }

        $datetime = date_format($datetime, "Y-m-d H:i:s");

        return Carbon::create($datetime);
    }
}
