<?php

namespace Onoi\CallbackContainer;

/**
 * @license GNU GPL v2+
 * @since 1.0
 * @deprecated since 1.1, use NullCallbackInstantiator
 *
 * @author mwjames
 */
class NullCallbackLoader implements CallbackLoader {

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function registerCallback( $handlerName, \Closure $callback ) {}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function registerExpectedReturnType( $handlerName, $type ) {}

	/**
	 * @since 1.1
	 *
	 * {@inheritDoc}
	 */
	public function registerCallbackContainer( CallbackContainer $callbackContainer ) {}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function load( $handlerName ) {}

	/**
	 * @since 1.2
	 *
	 * {@inheritDoc}
	 */
	public function create( $handlerName ) {}

	/**
	 * @since 1.0
	 *
	 * {@inheritDoc}
	 */
	public function singleton( $handlerName ) {}

}
