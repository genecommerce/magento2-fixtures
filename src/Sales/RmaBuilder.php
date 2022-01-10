<?php
declare(strict_types=1);

namespace TddWizard\Fixtures\Sales;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Rma\Api\Data\ItemInterface;
use Magento\Rma\Api\Data\RmaInterface;
use Magento\Rma\Api\RmaRepositoryInterface;
use Magento\Rma\Model\ItemFactory;
use Magento\Rma\Model\Rma;
use Magento\Rma\Model\Rma\Source\Status;
use Magento\Rma\Model\RmaFactory;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Builder to be used by fixtures
 */
class RmaBuilder
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var RmaFactory
     */
    private $rmaFactory;

    /**
     * @var RmaRepositoryInterface
     */
    private $rmaRepository;

    /**
     * @var ItemFactory
     */
    private $rmaItemFactory;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var int[]
     */
    private $orderItems;

    /**
     * @param RmaFactory $rmaFactory
     * @param RmaRepositoryInterface $rmaRepository
     * @param ItemFactory $rmaItemFactory
     * @param DateTime $dateTime
     * @param Order $order
     */
    final public function __construct(
        RmaFactory $rmaFactory,
        RmaRepositoryInterface $rmaRepository,
        ItemFactory $rmaItemFactory,
        DateTime $dateTime,
        Order $order
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->rmaRepository = $rmaRepository;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->dateTime = $dateTime;
        $this->order = $order;
        $this->orderItems = [];
    }

    /**
     * @param Order $order
     * @return RmaBuilder
     */
    public static function forOrder(
        Order $order
    ): RmaBuilder {
        $objectManager = Bootstrap::getObjectManager();
        return new static(
            $objectManager->create(RmaFactory::class),
            $objectManager->create(RmaRepositoryInterface::class),
            $objectManager->create(ItemFactory::class),
            $objectManager->create(DateTime::class),
            $order
        );
    }

    /**
     * @param int $orderItemId
     * @param int $qty
     * @return RmaBuilder
     */
    public function withItem(
        int $orderItemId,
        int $qty
    ): RmaBuilder {
        $builder = clone $this;
        $builder->orderItems[$orderItemId] = $qty;
        return $builder;
    }

    /**
     * @return RmaInterface
     */
    public function build(): RmaInterface
    {
        $rmaModel = $this->rmaFactory->create();
        $rmaData = [
            'status' => Status::STATE_PENDING,
            'date_requested' => $this->dateTime->gmtDate(),
            'order_id' => $this->order->getId(),
            'order_increment_id' => $this->order->getIncrementId(),
            'store_id' => $this->order->getStoreId(),
            'customer_id' => $this->order->getCustomerId(),
            'order_date' => $this->order->getCreatedAt(),
            'customer_name' => $this->order->getCustomerName(),
            'customer_custom_email' => $this->order->getCustomerEmail()
        ];
        $rmaModel->setData($rmaData);
        $rmaData['items'] = $this->buildRmaItems();;
        return $rmaModel->saveRma($rmaData) ?: $rmaModel;
    }

    /**
     * @return ItemInterface[]
     */
    private function buildRmaItems(): array
    {
        $rmaItems = [];
        foreach ($this->orderItems as $orderItemId => $qty) {
            $rmaItem = $this->rmaItemFactory->create();
            $rmaItem->setOrderItemId($orderItemId);
            $rmaItem->setQtyRequested($qty);
            $rmaItem->setResolution(5);
            $rmaItem->setReason(0);
            $rmaItem->setCondition(7);
            $rmaItems[] = $rmaItem->getData();
        }
        return $rmaItems;
    }
}
