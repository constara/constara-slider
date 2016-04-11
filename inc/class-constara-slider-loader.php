<?php

/**
 * Class for coordinating all hooks with
 * User: kutas
 * Date: 11.04.2016
 * Time: 1:27
 */
class Constara_Slider_Loader {

	protected $actions;

	public function __construct() {

		$this->actions = array();
	}

	public function add_action($hook, $component, $callback) {
		$this->actions = $this->add($this->actions, $hook, $component, $callback);
	}

	private function add($hooks, $hook, $component, $callback){
		$hooks[] = array(
			'hook'      => $hook,
			'component' => $component,
			'callback'  => $callback,
		);

		return $hooks;
	}

	public function run(){

		foreach ($this->actions as $action){
			add_action($action['hook'], array($action['component'], $action['callback']));
		}
	}

}