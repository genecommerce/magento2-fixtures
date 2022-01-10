<?php
declare(strict_types=1);

namespace TddWizard\Fixtures\Sales;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Rma\Api\RmaRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @internal Use RmaFixture::rollback() or RmaFixturePool::rollback() instead
 */
class RmaFixtureRollback
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RmaRepositoryInterface
     */
    private $rmaRepository;

    /**
     * @param Registry $registry
     * @param RmaRepositoryInterface $rmaRepository
     */
    public function __construct(
        Registry $registry,
        RmaRepositoryInterface $rmaRepository
    ) {
        $this->registry = $registry;
        $this->rmaRepository = $rmaRepository;
    }

    /**
     * @return RmaFixtureRollback
     */
    public static function create(): RmaFixtureRollback
    {
        $objectManager = Bootstrap::getObjectManager();
        return new self(
            $objectManager->get(Registry::class),
            $objectManager->get(RmaRepositoryInterface::class),
        );
    }

    /**
     * Roll back orders with associated customers and products.
     *
     * @param RmaFixture ...$rmaFixtures
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(RmaFixture ...$rmaFixtures): void
    {
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);
        foreach ($rmaFixtures as $rmaFixture) {
            $this->rmaRepository->delete($rmaFixture->getRma());
        }
        $this->registry->unregister('isSecureArea');
    }
}
