<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional\Ticket;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Tests\OrmFunctionalTestCase;

final class GH7761Test extends OrmFunctionalTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        parent::setUp();

        $this->setUpEntitySchema([
            GH7761Entity::class,
            GH7761ChildEntity::class,
        ]);

        $parent = new GH7761Entity();
        $child  = new GH7761ChildEntity();
        $parent->children->add($child);

        $this->_em->persist($parent);
        $this->_em->persist($child);
        $this->_em->flush();
        $this->_em->clear();
    }

    public function testCollectionClearDoesNotClearIfNotPersisted() : void
    {
        /** @var GH7761Entity $entity */
        $entity = $this->_em->find(GH7761Entity::class, 1);
        $entity->children->clear();
        $this->_em->persist(new GH7761Entity());
        $this->_em->flush();

        $this->_em->clear();

        $entity = $this->_em->find(GH7761Entity::class, 1);
        self::assertCount(1, $entity->children);

        $this->_em->clear();
    }
}

/**
 * @Entity
 * @ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class GH7761Entity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    public $id;

    /**
     * @ManyToMany(targetEntity="Doctrine\Tests\ORM\Functional\Ticket\GH7761ChildEntity", cascade={"all"})
     * @JoinTable(name="gh7761_to_child",
     *     joinColumns={@JoinColumn(name="entity_id")},
     *     inverseJoinColumns={@JoinColumn(name="child_id")}
     * )
     */
    public $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }
}

/**
 * @Entity
 * @ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class GH7761ChildEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    public $id;
}
