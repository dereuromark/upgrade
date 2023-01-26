<?php

namespace SomePlugin\Controller;
class SomeController {

    public $paginate = ['order' => ['Companies.name' => 'ASC']];

    public function simple() {
		$modelName = $this->getController()->loadModel()->getAlias();
	}

	public function multiLine() {
		$modelName = $this->X->loadModel()
			->getAlias();
	}

}
