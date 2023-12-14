<?php

namespace OrderNote\Api\Resource;

use ApiPlatform\Metadata\Operation;
use OrderNote\Model\Map\OrderNoteTableMap;
use OrderNote\Model\OrderNote;
use OrderNote\Model\OrderNoteQuery;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Map\TableMap;
use SiretManagement\Model\SiretCustomer;
use SiretManagement\Model\SiretCustomerQuery;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Resource\Order;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\ResourceAddonInterface;
use Thelia\Api\Resource\ResourceAddonTrait;

class Note implements ResourceAddonInterface
{
    use ResourceAddonTrait;

    public ?int $id = null;

    public int $orderId;

    #[Groups([Order::GROUP_READ_SINGLE, Order::GROUP_WRITE])]
    public ?string $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Note
    {
        $this->id = $id;
        return $this;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): Note
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): Note
    {
        $this->note = $note;
        return $this;
    }

    public static function getResourceParent(): string
    {
        return Order::class;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new OrderNoteTableMap();
    }
    public static function extendQuery(ModelCriteria $query, Operation $operation = null, array $context = []): void
    {
        if (OrderNoteQuery::create()->filterByOrderId($query->get('order.id'))->findOne() === null){
            return;
        }
        $tableMap = static::getPropelRelatedTableMap();
        $query->useOrderNoteQuery()->endUse();

        foreach ($tableMap->getColumns() as $column) {
            $query->withColumn(OrderNoteTableMap::COL_NOTE, 'Note_note');
        }
    }

    public function buildFromModel(ActiveRecordInterface $activeRecord, PropelResourceInterface $abstractPropelResource): ResourceAddonInterface
    {
        if (OrderNoteQuery::create()->filterByOrderId($activeRecord->getId())->findOne() === null){
            return $this;
        }
        $this->note = $activeRecord->hasVirtualColumn('Note_note') ? $activeRecord->getVirtualColumn('Note_note') : null;
        return $this;
    }

    public function buildFromArray(array $data, PropelResourceInterface $abstractPropelResource): ResourceAddonInterface
    {
        $this->note = $data['note'];
        return $this;
    }

    public function doSave(ActiveRecordInterface $activeRecord, PropelResourceInterface $abstractPropelResource): void
    {
        $model = new OrderNote();
        if (isset($activeRecord->getOrderNotes()->getData()[0])){
            $id = $activeRecord->getOrderNotes()->getData()[0]->getId();
            $model = OrderNoteQuery::create()->filterById($id)->findOne();
        }

        $model->setNote($this->getNote());
        $model->setOrderId($activeRecord->getId());


        $model->save();
    }

    public function doDelete(ActiveRecordInterface $activeRecord, PropelResourceInterface $abstractPropelResource): void
    {
        foreach ($activeRecord->getOrderNotes() as $siret){
            OrderNoteQuery::create()->findOneById($siret->getId())->delete();
        }
    }


}
