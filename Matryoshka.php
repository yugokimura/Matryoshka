<?php

class Matryoshka {
	
	protected $limit;
	const RESULT_CLASS = 1;
	const RESULT_ARRAY = 2;

	function __construct() {
		$this->setLimitDepth(100);
	}

	function __destruct() {

	}

	public function setLimitDepth( $limit = 100 ) {
		$this->limit = $limit;
	}

	function query( $query = '' ,$mode = self::RESULT_ARRAY) {
		if( $mode == self::RESULT_CLASS ) {
			return $this->getObjectsClass( $query );
		} else if( $mode == self::RESULT_ARRAY) {
			return $this->getObjectsArray ( $query );
		}
	}

	public function getObjectsClass( $query, $currentDepth = 0 ) {

		if(preg_match_all('/([0-9a-zA-Z_-]+)(\.(?:[0-9a-zA-Z_-]+)+\((?:(?:[^()]|(?R))*)\))*/', $query, $matches, PREG_SET_ORDER)){
			$currentDepth++;
			$objectsClass = new stdClass();
			foreach($matches as $objects){
				$objectsClass->{$objects[1]} = $this->getAttributesClass($objects[0], $currentDepth);
			}
			return $objectsClass;
		}
		return new stdClass();
	}

	public function getAttributesClass( $query, $currentDepth = 0 ) {

		if(preg_match_all('/\.([0-9a-zA-Z_-]+)+\((([^()]|(?R))*)\)/', $query, $matches, PREG_SET_ORDER)) {
			$currentDepth++;
			$attributesClass = new stdClass();
			foreach($matches as $attributes) {

				switch($attributes[1]){
					case 'in':
						$objectsClass = $this->getObjectsClass($attributes[2], $currentDepth);
						if( $objectsClass != null ){
							$attributesClass = (object) array_merge( (array) $attributesClass, (array) $objectsClass);
						}

						break;
					default:
						$splits = preg_split("/[,]+/", $attributes[2], -1, PREG_SPLIT_NO_EMPTY);
						if($splits != null and count($splits) > 1) {
							$attributesClass->{$attributes[1]} = $splits;
						} else {
							$attributesClass->{$attributes[1]} = $attributes[2];
						}
						break;
				}
			}

			return $attributesClass;
		}
		return new stdClass();
	}

	public function getObjectsArray( $query, $currentDepth = 0 ) {

		if(preg_match_all('/([0-9a-zA-Z_-]+)(\.(?:[0-9a-zA-Z_-]+)+\((?:(?:[^()]|(?R))*)\))*/', $query, $matches, PREG_SET_ORDER)){
			$currentDepth++;
			$objectArray = array();
			foreach($matches as $objects){

				$object = new stdClass();    // フィールドオブジェクトを作成
				$object->name = $objects[1]; // フィールド名を格納

				$attributesArray = $this->getAttributesArray($objects[0], $currentDepth); // 属性配列を取得
				if( $attributesArray != null ) {
					$object->attributes = $attributesArray; // 属性が存在する場合は値を挿入
				} else {
					$object->attributes = array(); // 属性が存在しない場合は空配列
				}
				$objectArray[] = $object; //ベースオブジェクトの配列に格納
			}
			return $objectArray;
		}
		return array();
	}

	public function getAttributesArray($query, $currentDepth = 0){

		if(preg_match_all('/\.([0-9a-zA-Z_-]+)+\((([^()]|(?R))*)\)/', $query, $matches, PREG_SET_ORDER)) {
			$currentDepth++;
			$attributesArray = array();
			foreach($matches as $attributes) {
				//print $attributes[1] . "\n";

				$attribute = new stdClass();
				$attribute->name = $attributes[1];

				switch($attributes[1]){
					case 'in':
						$ObjectArray = $this->getObjectsArray($attributes[2], $currentDepth);
						if( $ObjectArray != null ){
							$attribute->value = $ObjectArray;
						}
						break;
					default:
						$splits = preg_split("/[,]+/", $attributes[2], -1, PREG_SPLIT_NO_EMPTY);
						if($splits != null and count($splits) > 1 ) {
							$attribute->value = $splits;
						} else {
							$attribute->value = $attributes[2];
						}
						break;
				}

				$attributesArray[] = $attribute;
			}
			return $attributesArray;
		}
		return array();
	}
}
?>
