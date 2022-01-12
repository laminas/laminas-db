<?php

namespace Laminas\Db\TableGateway\Feature;

/**
 * EventFeature event constants.
 *
 * This moves the constants introduced in {@link https://github.com/zendframework/zf2/pull/7066}
 * into a separate interface that EventFeature implements; the change keeps
 * backwards compatibility, while simultaneously removing the need to add
 * another hard dependency to the component.
 */
interface EventFeatureEventsInterface
{
    public const EVENT_PRE_INITIALIZE  = 'preInitialize';
    public const EVENT_POST_INITIALIZE = 'postInitialize';

    public const EVENT_PRE_SELECT  = 'preSelect';
    public const EVENT_POST_SELECT = 'postSelect';

    public const EVENT_PRE_INSERT  = 'preInsert';
    public const EVENT_POST_INSERT = 'postInsert';

    public const EVENT_PRE_DELETE  = 'preDelete';
    public const EVENT_POST_DELETE = 'postDelete';

    public const EVENT_PRE_UPDATE  = 'preUpdate';
    public const EVENT_POST_UPDATE = 'postUpdate';
}
