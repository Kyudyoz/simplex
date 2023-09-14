<?php
namespace App\Http\Helpers;

class simplexMethod
{
	//function for getting the column with the smallest value; returns the index of the minimum column
	public function getMinimumColumn($valuesArray, $num, $n)
	{
		for($i=0; $i<=$n+1; $i++)
		{
			if($valuesArray[$num][$i]<0)
			{
				$index=$i;
				break;
			}
		}
		if($i==$n+2)
			return $i;
		for($i=0; $i<=$n+1; $i++)
		{
			if($valuesArray[$num][$i]<0)
			{
				if($valuesArray[$num][$i]<$valuesArray[$num][$index])
					$index=$i;
			}
		}
		return $index;
	}

	//function for getting the row with the smallest ratio; returns the index of the pivot row
	public function getMinimumRow($valuesArray, $num, $minCol, $n)
	{
		for($i=0; $i<$num; $i++)
		{
			if($valuesArray[$i][$minCol]>0)
			{
				$index=$i;
				break;
			}
		}
		if($i==$num)
			return $i;
		for($i=0; $i<$num; $i++)
		{
			if($valuesArray[$i][$minCol]>0)
			{
				if($valuesArray[$i][$n+1]/$valuesArray[$i][$minCol] < $valuesArray[$index][$n+1]/$valuesArray[$index][$minCol])
					$index=$i;
			}
		}
		return $index;
	}

	//function for transposing a matrix; returns the transposed matrix
	public function transposeMatrix($valuesArray, $rows, $cols)
	{
		$array = array();
		for($i=0; $i<=$cols; $i++)
		{
			for($j=0; $j<=$rows; $j++)
				$array[$i][$j]=$valuesArray[$j][$i];
		}
		return $array;
	}
}
?>
