<?php
declare(strict_types=1);

namespace TddWizard\Fixtures\Sales;

use Magento\Rma\Api\Data\RmaInterface;

class RmaFixturePool
{

    /**
     * @var RmaFixture[]
     */
    private $rmaFixtures = [];

    public function add(RmaInterface $rma, string $key = null): void
    {
        if ($key === null) {
            $this->rmaFixtures[] = new RmaFixture($rma);
        } else {
            $this->rmaFixtures[$key] = new RmaFixture($rma);
        }
    }

    /**
     * Returns rma fixture by key, or last added if key not specified
     *
     * @param string|null $key
     * @return RmaFixture
     */
    public function get(string $key = null): RmaFixture
    {
        if ($key === null) {
            $key = \array_key_last($this->rmaFixtures);
        }
        if ($key === null || !array_key_exists($key, $this->rmaFixtures)) {
            throw new \OutOfBoundsException('No matching rma found in fixture pool');
        }
        return $this->rmaFixtures[$key];
    }
}
