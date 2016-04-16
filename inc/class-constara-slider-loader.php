<?php

/**
 * Class for coordinating all hooks with
 * User: kutas
 * Date: 11.04.2016
 * Time: 1:27
 */
class Constara_Slider_Loader {

	protected $actions;

	protected $filters;

	protected $shortcodes;

	public function __construct() {

		$this->actions = array();
		$this->shortcodes = array();

	}

	public function add_action($hook, $component, $callback, $priority = 10, $args = 1) {
		$this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $args);
	}

	public function add_filter($hook, $component, $callback, $priority = 10, $args = 1){
		$this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $args);
	}

	public function add_shortcode($tag, $component, $callback){
		$this->shortcodes = $this->add($this->shortcodes, $tag, $component, $callback);
	}

	private function add($hooks, $hook, $component, $callback, $priority = 10, $args = 1){
		$hooks[] = array(
			'hook'      => $hook,
			'component' => $component,
			'callback'  => $callback,
			'priority'	=> $priority,
			'args'		=> $args,
		);

		return $hooks;
	}

	public function run(){

		foreach ($this->actions as $action){
			add_action($action['hook'], array($action['component'], $action['callback']), $action['priority'], $action['args']);
		}

		foreach ($this->filters as $filter) {
			add_filter($filter['hook'], array($filter['component'], $filter['callback']), $filter['priority'], $filter['args']);
		}

		foreach ($this->shortcodes as $shortcode) {
			add_shortcode($shortcode['hook'], array($shortcode['component'], $shortcode['callback']));
		}
	}

}