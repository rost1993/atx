<?php

namespace IcKomiApp\core;

use IcKomiApp\core\Functions;

/*
	
*/
class Logic {

	protected $array_logic = [];
	protected $array_data = [];

	protected $number_logic = 0;
	protected $message_error = '';

	/*
		
	*/
	protected $array_check_function = [
		'!' => 'check_mandatory',
		'!=' => 'check_not_equal',
		'=' => 'check_equal',
		'type' => 'check_type',
		'IN' => 'check_in',
		'NOT IN' => 'check_not_in',
		'>' => 'check_more',
		'<' => 'check_less',
		'>=' => 'check_more_equal',
		'<=' => 'check_less_equal',
		'IF' => 'check_if',
		'IF IN' => 'check_if_in',
		'LENGTH=' => 'check_length',
	];

	/*
	protected $array_logic = [
		['number' => '1',
		 'type' => '1',
		 'operation' => '!',
		 'field1' => 'LASTNAME',
		 'field2' => '',
		 'value1' => '',
		 'value2' => '',
		 'true' => '',
		 'false' => '',
		 'message' => 'Не заполнен обязательный реквизит: Фамилия!',
			]
		];
	*/

	public function __construct($array_data, $array_logic) {
		$this->array_data = $array_data;
		$this->array_logic = $array_logic;
	}

	/*
		Стержневая функция проверки логических условий
	*/
	public function check_logic(&$number_logic, &$message_error) {
		if(!is_array($this->array_data))
			return false;

		if(!is_array($this->array_logic))
			return false;

		foreach ($this->array_logic as $logic) {
			if($logic['type'] != 1)
				continue;

			//echo ' ' . $logic['number'];

			if(!$this->check_current_logic($logic)) {
				$number_logic = $this->number_logic;
				$message_error = $this->message_error;
				return false;
			}
		}

		return true;
	}

	private function check_current_logic($logic) {
		if(!array_key_exists($logic['operation'], $this->array_check_function)) {
			$this->number_logic = $logic['number'];
			$this->message_error = 'Не найден оператор сравнения!';
			return false;
		}

		$function = $this->array_check_function[$logic['operation']];
		$flg = $this->$function($this->array_data, $logic);


//Functions::debug($flg);

		$new_logic = 0;

		if($flg) {
			if(empty($logic['true']) && empty($logic['false'])) {
				return true;
			} else {
				if(empty($logic['true'])) {
					return true;
				} else {
					$new_logic = $logic['true'];
				}
			}
		} else {
			if(empty($logic['true']) && empty($logic['false'])) {
				if(empty($logic['false'])) {
					$this->number_logic = $logic['number'];
					$this->message_error = $logic['message'];
					return false;
				}
			} else {
				if(empty($logic['false'])) {
					return true;
				} else {
					$new_logic = $logic['false'];
				}
			}
		}

		// Если не нашли логическое условие то выходим в зависимости от проверки текущего условия
		if(($key = $this->search_logic($new_logic)) == -1) {
			$this->number_logic = $logic['number'];
			$this->message_error = $logic['message'];
			return $flg;
		}

		return $this->check_current_logic($this->array_logic[$key]);
	}

	/*
		Функция поиска логического условия
	*/
	private function search_logic($number_logic) {
		foreach ($this->array_logic as $key => $logic)
			if($logic['number'] == $number_logic)
				return $key;
		return -1;
	}

	/*
		Проверка на обязательность заполнения поля
	*/
	private function check_mandatory($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(empty($data[$logic['field1']]))
			return false;

		$value = $data[$logic['field1']];
		if(mb_strlen(trim($value)) == 0)
			return false;

//		Functions::debug($data[$logic['field1']]);

		if(((string)$value) == '0')
			return false;

		if($value === null)
			return false;
		return true;
	}

	/*
		Проверка на не равенство
	*/
	private function check_not_equal($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(empty($logic['field2'])) {
			if($data[$logic['field1']] == $logic['value1'])
				return false;
		} else {
			if(!array_key_exists($logic['field2'], $data))
				return false;

			if($data[$logic['field1']] == $data[$logic['field2']])
				return false;
		}
		return true;
	}

	/*
		Проверка на равенство
	*/
	private function check_equal($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(empty($logic['field2'])) {
			if($data[$logic['field1']] != $logic['value1'])
				return false;
		} else {
			if(!array_key_exists($logic['field2'], $data))
				return false;

			if($data[$logic['field1']] != $data[$logic['field2']])
				return false;
		}
		return true;
	}

	/*
		Проверка на тип данных
	*/
	private function check_type($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		$value = $data[$logic['field1']];
		$flg = true;
		switch(trim($logic['value1'])) {
			case 'char':
			case 'string':
				$flg = is_string($value);
				break;

			case 'number':
				$flg = is_numeric($value);
				break;

			case 'array':
				$flg = is_array($value);
				break;

			case 'date':
				$flg = $this->is_date($value);
				break;

			default:
				$flg = true;
				break;
		}
		return $flg;
	}

	/*
		Функция, которая проверяет передана дата или нет
		$date - строка проверяемая на дату
		Возвращаемое значение: TRUE - если дата, FALSE - если не дата
	*/
	private function is_date($date) {
		return is_numeric(strtotime($date));
	}

	/*
		Проверка значения на вхождение
	*/
	private function check_in($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(is_array($logic['value1'])) {
			foreach($logic['value1'] as $item)
				if($data[$logic['field1']] == $item)
					return true;
		} else {
			$temp = explode(',', $logic['value1']);
			for($i = 0; $i < count($temp); $i++)
				if($data[$logic['field1']] == $temp[$i])
					return true;
		}
		return false;
	}

	/*
		Проверка значения на невхождение
	*/
	private function check_not_in($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(is_array($logic['value1'])) {
			foreach($logic['value1'] as $item)
				if($data[$logic['field1']] == $item)
					return false;
		} else {
			$temp = explode(',', $logic['value1']);
			for($i = 0; $i < count($temp); $i++)
				if($data[$logic['field1']] == $temp[$i])
					return false;
		}
		return true;
	}

	/*
		Проверка на большее значение
	*/
	private function check_more($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(empty($logic['field2'])) {
			if($data[$logic['field1']] > $logic['value1'])
				return true;
		} else {
			if(!array_key_exists($logic['field2'], $data))
				return false;

			if($data[$logic['field1']] > $data[$logic['field2']])
				return true;
		}
		return false;
	}

	/*
		Проверка на меньшее значение
	*/
	private function check_less($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;
		
		if(empty($logic['field2'])) {
			if($data[$logic['field1']] < $logic['value1'])
				return true;
		} else {
			if(!array_key_exists($logic['field2'], $data))
				return false;

			if($data[$logic['field1']] < $data[$logic['field2']])
				return true;
		}
		return false;
	}

	/*
		Проверка на больше или равно
	*/
	private function check_more_equal($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(empty($logic['field2'])) {
			if($data[$logic['field1']] >= $logic['value1'])
				return true;
		} else {
			if(!array_key_exists($logic['field2'], $data))
				return false;

			if($data[$logic['field1']] >= $data[$logic['field2']])
				return true;
		}
		return false;
	}

	/*
		Проверка на меньше или равно
	*/
	private function check_less_equal($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(empty($logic['field2'])) {
			if($data[$logic['field1']] <= $logic['value1'])
				return true;
		} else {
			if(!array_key_exists($logic['field2'], $data))
				return false;

			if($data[$logic['field1']] <= $data[$logic['field2']])
				return true;
		}
		return false;
	}

	/*
	*/
	private function check_if($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		if(!array_key_exists($logic['field2'], $data))
			return false;

		$value1 = $data[$logic['field1']];
		$value2 = $data[$logic['field2']];

		if(($value1 == $logic['value1']) && ($value2 == $logic['value2']))
			return true;

		return false;
	}

	/*
	*/
	private function check_if_in($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;
		
		if(!array_key_exists($logic['field2'], $data))
			return false;

		$value1 = $data[$logic['field1']];
		$value2 = $data[$logic['field2']];

		$flg1 = $flg2 = false;

		if(is_array($logic['value1'])) {
			foreach($logic['value1'] as $item) {
				if($value1 == $item) {
					$flg1 = true;
					break;
				}
			}
		} else {
			$temp = explode(',', $logic['value1']);
			for($i = 0; $i < count($temp); $i++) {
				if($value1 == $temp[$i]) {
					$flg1 = true;
					break;
				}
			}
		}

		if(is_array($logic['value2'])) {
			foreach($logic['value2'] as $item) {
				if($value2 == $item) {
					$flg2 = true;
					break;
				}
			}
		} else {
			$temp = explode(',', $logic['value2']);
			for($i = 0; $i < count($temp); $i++) {
				if($value2 == $temp[$i]) {
					$flg2 = true;
					break;
				}
			}
		}

		return ($flg1 & $flg2);
	}

	/*
		Проверка на длину. Если длина поля field1 не равна длине value1 то возвращаем ошибку
	*/
	private function check_length($data, $logic) {
		if(!array_key_exists($logic['field1'], $data))
			return false;

		$length = ((empty($logic['value1'])) || (mb_strlen($logic['value1']) == 0)) ? 0 : $logic['value1'];

		if(mb_strlen(trim($data[$logic['field1']])) != $length)
			return false;
		return true;
	}

}