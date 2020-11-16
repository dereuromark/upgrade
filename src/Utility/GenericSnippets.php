<?php

namespace Cake\Upgrade\Utility;

class GenericSnippets {

	/**
	 * @return array
	 */
	public function snippets(): array {
		$list = [
			[
				'Plugin routes() signature',
				'#public function routes\(\$routes\)#i',
				'public function routes(\Cake\Routing\RouteBuilder $routes)',
			],
			[
				'Plugin routes() return type',
				'#public function routes\((.+)\)(?!:)#i',
				'public function routes(\1): void',
			],
			[
				'->newEntity()',
				'#-\>newEntity\(\)#',
				'->newEmptyEntity()',
			],
			[
				'Cake\Http\Exception namespace',
				'#\bCake\\\\Network\\\\Exception\\\\#',
				'Cake\\Http\\Exception\\',
			],
			[
				'$this->request->url',
				'#-\>request-\>url\b#',
				'->request->getRequestTarget()',
			],
		];

		return $list;
	}

}