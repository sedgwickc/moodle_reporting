<?php

class dept_sum {

	private $cat_sums;
	private $remarks = 'None';
	private $dept;
	private $categories;
	private $category_hours;


	// $new_categories = array('id'=>'name')
	public function init_categories( $new_categories ){
	
		$this->cat_sums = array();
		$this->categories = $new_categories;
		foreach( $this->categories as $id=>$name ){
			$this->cat_sums[$id] = 0;
		}
	}

	public function get_dept_total(){
		$sum = 0;
		foreach( $this->cat_sums as $cat=>$hours ){
			$sum += $hours;
		}
		return $sum;
	}

	public function increment_category( $id ){
		$this->cat_sums[$id] += 1;
	}

	public function get_dept(){
		return $this->dept;
	}

	public function get_remarks(){
		return $this->remarks;
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
	
	public function set_hours_soft($num){
		 $this->hours_soft = $num;
	}

	public function set_remarks($new_remarks){
		$this->remarks = $new_remarks;
	}
}
