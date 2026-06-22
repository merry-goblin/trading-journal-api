<?php

namespace App\DTO\FrontApi\Import;

class ImportPositionItem
{
    public string $symbol;
    public string $direction;
    public string $openedAt;
    public string $closedAt;
    public string $entryPrice;
    public string $exitPrice;
    public ?string $stopLoss   = null;
    public ?string $takeProfit = null;
    public string $volume;
    public string $pnl;
    public bool $isBacktest = true; // import = backtest par defaut
}
