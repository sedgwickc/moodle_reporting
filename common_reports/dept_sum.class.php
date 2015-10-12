<?php

class dept_sum {

	private $cat_sums;
	private $dept;
	private $categories;
	private $category_hours;


	// $new_categories = array('id'=>'name')
	public function init_categories( $new_categories ){
	
		$this->cat_sums = array();
		$this->categories = $new_categories;
		foreach( $this->categories as $id=>$name ){
			$this->cat_sums[$id] = 0.0;
		}
	}

	public function get_dept_total(){
		$sum = 0;
		foreach( $this->cat_sums as $cat=>$hours ){
			$sum += $hours;
		}
		return $sum;
	}

	public function increment_category( $id, $minutes ){
		$this->cat_sums[$id] += $minutes;
	}

	public function get_dept(){
		return $this->dept;
	}

	public function get_category_hours( $id ){
		return $this->cat_sums[$id];
	}

	public function get_categories(){
		return $this->categories;
	}

	public function set_dept( $new_dept ){
		$this->dept = $new_dept;
	}
	
}
