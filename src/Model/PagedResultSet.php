<?php

declare(strict_types=1);

namespace Brightspace\Api\Core\Model;

use Gadget\Io\Cast;

/**
 * @template T
 */
final class PagedResultSet
{
    /**
     * @template TValue
     * @param (callable(string|null $bookmark): self<TValue>) $fetchNext
     * @return iterable<TValue>
     */
    public static function forEach(callable $fetchNext): iterable
    {
        $bookmark = null;
        do {
            $page = $fetchNext($bookmark);
            yield from $page->items;
            $bookmark = $page->pagingInfo->bookmark;
        } while ($bookmark !== null && $bookmark !== '');
    }


    /**
     * @template TValue
     * @param mixed $values
     * @param (callable(mixed $v): TValue) $toValue
     * @return self<TValue>
     */
    public static function create(
        mixed $values,
        callable $toValue
    ): self {
        $values = Cast::toArray($values);
        return new self(
            pagingInfo: PagingInfo::create($values['PagingInfo'] ?? null),
            items: Cast::toTypedArray($values['Items'] ?? [], $toValue)
        );
    }


    /**
     * @param PagingInfo $pagingInfo
     * @param T[] $items
     */
    public function __construct(
        public PagingInfo $pagingInfo,
        public array $items
    ) {
    }
}
