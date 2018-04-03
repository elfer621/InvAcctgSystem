<?php
class numwordsnew { 
	function intToWords($x)
		{

		$nwords = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 
						'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 
						'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 
						'nineteen', 'twenty', 30 => 'thirty', 40 => 'forty', 
						50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 
						90 => 'ninety',
						'dollars' => 'dollars', 'cents' => 'cents');

			if(!is_float($x) && !is_numeric($x))
			{
				$w = '#';
			}
			else
			{
				if($x < 0)
				{
					$w = 'minus '; 
					$x = -$x; 
				}
				else
				{
					$w = ''; 
				}
				if($x < 21)
				{
					$w .= $nwords[$x];
				}
				else if($x < 100)
				{
					$w .= $nwords[10 * floor($x/10)];
					$r = fmod($x, 10);
					if($r > 0)
					{
						$w .= '-'. $nwords[$r];
					}
					
					/*if(is_float($x))
					{
						$w .= ' ' . $nwords['cents'];
					}
					else if(is_int($x))
					{
						$w .= ' ' . $nwords['dollars'];
					}*/
				}
				else if($x < 1000)
				{
					$w .= $nwords[floor($x/100)] .' hundred';
					$r = fmod($x, 100);
					if($r > 0)
					{
						//$w .= ' and '. $this->convertCurrencyToWords($r);
						$w .= ' '. $this->convertCurrencyToWords($r);
					}
				}
				else if($x < 1000000)
				{
					$w .= $this->convertCurrencyToWords(floor($x/1000)) .' thousand';
					$r = fmod($x, 1000);
					if($r > 0)
					{
						$w .= ' '; 
						if($r < 100)
						{
							$w .= ' and '; 
						}
						$w .= $this->convertCurrencyToWords($r); 
					}
				}
				else
				{
					$w .= $this->convertCurrencyToWords(floor($x/1000000)) .' million'; 
					$r = fmod($x, 1000000);
					if($r > 0)
					{ 
						$w .= ' ';
						if($r < 100)
						{
							$word .= ' and ';
						}
						$w .= $this->convertCurrencyToWords($r);
					}
				}
			}
			return $w; 
		}
		
		function convertCurrencyToWords($number) {
			if(!is_numeric($number)) return false;
			$nums = explode('.', $number);
			$out = $this->intToWords($nums[0]);
			if(($nums[1]*1)!=0) {
				$out .= ' pesos and '.$this->intToWords($nums[1]*1).' cents';
				//$out .= ' pesos and '.$nums[1].' cents';
			}
		return $out;
		
	}
}
?>