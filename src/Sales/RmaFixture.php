<?php
declare(strict_types=1);

namespace TddWizard\Fixtures\Sales;

use Magento\Rma\Api\Data\RmaInterface;

class RmaFixture
{
    /**
     * @var RmaInterface
     */
    private $rma;

    public function __construct(RmaInterface $rma)
    {
        $this->rma = $rma;
    }

    public function getRma(): RmaInterface
    {
        return $this->rma;
    }

    public function getId(): int
    {
        return (int) $this->rma->getEntityId();
    }

    public function rollback(): void
    {
        RmaFixtureRollback::create()->execute($this);
    }
}
