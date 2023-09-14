@extends('layouts.main')
@section('section')
@include('library')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/style.css') }}" />

<div>
    <h2 class="heading">Hasil Perhitungan Simpleks</h2>
</div>
<div class="container bg-dark text-white">
    <p><a href="/" class="btn btn-light mt-4">Ulang</a></p>
<?php
    if(isset($_POST['submit']))
    {
        if($_POST['category']=='max')
        {
            $flag=0;
            $minRow=0;
            //solves for the maximization problem
            //get the number of constraints from the hidden form input named numbers and put it into the array named num
            //$num[0]=num of variables and $num[1]=num of constraints
            $num = array();
            $i=0;
            $token=strtok($_POST['numbers'], ',');
            while($token)
            {
                $num[$i++]=$token;
                $token=strtok(',');
            }

            //$n+1 would be the total number of columns
            $n=$num[0]+$num[1];

            //get the values from the form and insert it into the array
            $valuesArray = array();
            for($k=1; $k<=$num[1]+1; $k++)
            {
                for($j=1; $j<=$num[0]; $j++)
                {
                    //first rows would have the coefficients of the constraints
                    if($k!=$num[1]+1)
                        $valuesArray[$k-1][$j-1]=floatval($_POST["kendala$k$j"]);
                    //last row would have the coefficients of the objective function
                    else
                        $valuesArray[$k-1][$j-1]=floatval($_POST["variabel$j"])*-1;
                }
            }

            //add the slack variables to the matrix, the number of slack variables depends on the number of constraints
            for($i=1; $i<=$num[1]+1; $i++)
            {
                $k=0;
                for($j=$num[0]; $j<=$n; $j++)
                {
                    if($k!=($i-1))
                        $valuesArray[$i-1][$j]=0;
                    else if ($i==$num[1]+1)
                        $valuesArray[$i-1][$j]=1;
                    else
                    {
                        //if the inequality sign of the constraint is less than, the sign of the slack variable would be postive
                        if($_POST["tanda$i"]=="kurangDari")
                            $valuesArray[$i-1][$j]=1;
                        //if the inequality sign of the constraint is greater than, the sign of the slack variable would be negative
                        else
                            $valuesArray[$i-1][$j]=-1;
                    }
                    $k++;
                }
            }

            //insert the values from the form input with the name answer into the last column of the matrix - this are the constants of the equation
            for($i=1; $i<=$num[1]+1; $i++)
            {
                if($i!=$num[1]+1)
                    $valuesArray[$i-1][$n+1]=floatval($_POST["SA$i"]);
                //the last column would have the value of zero since there is no constant in the objective function
                else
                    $valuesArray[$i-1][$n+1]=0;
            }

            //display the initial tableau
            echo"<br /><p class='text-center'>Tabel Awal:</p> <br /><br />";
            echo "<table border='1' id='myTable'>";
                for ($th=1; $th <= $num[0] ; $th++) {
                    echo "<th class='text-center'>X".$th."</th>";
                }
                for ($s=1; $s <= $num[1] ; $s++) {
                    echo "<th class='text-center'>S".$s."</th>";
                }
                echo "<th class='text-center'>Z</th>";
                echo "<th class='text-center'>Nilai Kanan</th>";

            for($i=0; $i<=$num[1]; $i++)
            {
                echo "<tr>";
                for($j=0; $j<=$n+1; $j++)
                    echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<br />";

            //display the basic solution for the problem
            echo "<p class='text-center'>Nilai Kanan Awal:</p><br /><br />";
            echo "<table border='1' id='myTable'>
            <tr>";
            for($i=0; $i<=$n; $i++)
            {
                //the $ctr would be the variable that would count the number of 1s in the row
                $ctr=0;
                for($j=0; $j<=$num[1]; $j++)
                {
                    //if it encounters a non-zero, check if the value of the non-zero is 1
                    if($valuesArray[$j][$i]!=0)
                    {
                        //if it encounters a 1 in the array, increment $ctr and assign $j to $index
                        if($ctr==0 && $valuesArray[$j][$i]==1)
                        {
                            $index=$j;
                            $ctr++;
                        }
                        //if it is not equal to 1, or there are more than one 1s in the row, stop the loop
                        else
                            break;
                    }
                }
                //if $ctr is exactly equal to 1, it means that the column is cleared, meaning there is one row that has a value of 1 and the rest is zero
                if($ctr==1)
                {
                    //check if $i+1 <=$num[0], if this is true, value it is getting is for the Xs
                    if(($i+1)<=$num[0])
                    {
                        $sub=$i+1;
                        echo "<td id='each'> x".$sub." = ".$valuesArray[$index][$n+1]."</td>";
                    }
                    //if not, the value it is getting is for the slack variables and the z
                    else
                    {
                        $sub=$i-$num[0]+1;
                        //if $sub<=$num[1], the values are for the slack variables
                        if($sub<=$num[1])
                            echo "<td id='each'> s".$sub." = ".$valuesArray[$index][$n+1]."</td>";
                        //if not, then the value is for z
                        else
                            echo "<td id='each'> z = ".$valuesArray[$index][$n+1]."</td>";
                    }
                }
                //if $ctr!=1, then the column is not cleared, the variables would have a value of 0
                else
                {
                    if(($i+1)<=$num[0])
                    {
                        $sub=$i+1;
                        echo "<td id='each'> x".$sub." = 0</td>";
                    }
                    else
                    {
                        $sub=$i-$num[0]+1;
                        if($sub<=$num[1])
                            echo "<td id='each'> s".$sub." = 0</td>";
                        else
                            echo "<td id='each'> z = 0</td>";
                    }
                }
            }
            echo "</tr>
            </table>";

            $manager=new simplexMethod;

            //the maximum number of iterations would be 100, if it excedes the maximum, the problem is infeasible
            for($max=0; $max<100; $max++)
            {
                //get the index of the column with the smallest value
                $minCol=$manager->getMinimumColumn($valuesArray, $num[1], $n);

                //if there are no negative values, the problem is already maximized
                if($minCol==$n+2)
                    break;

                //get the index of the row with the smallest ratio a/b -> a is the rightmost column and b is the positive entry from the minCol
                $minRow=$manager->getMinimumRow($valuesArray, $num[1], $minCol, $n);
                //if there are no non-negative or zero, the problem is infeasible
                if($minRow==$num[1])
                {
                    $flag=1;
                    echo "<p class='final'>Masalah tidak bisa dihitung </p>";
                    break;
                }

                //display the iteration number
                $itr=$max+1;
                echo "<br /><p class='text-center'>Iterasi ke-".$itr.":</p><br /><br />";

                //normalize the pivot row
                //divide the pivot row by the pivot element
                for($i=0; $i<=$n+1; $i++)
                    if($i!=$minCol)
                        $valuesArray[$minRow][$i]=$valuesArray[$minRow][$i]/$valuesArray[$minRow][$minCol];
                $valuesArray[$minRow][$minCol]=1;

                //make the rest of the elements of the pivot column 0
                for($i=0; $i<=$num[1]; $i++)
                {
                    if($i!=$minRow)
                    {
                        for($j=0; $j<=$n+1; $j++)
                            if($j!=$minCol)
                                $valuesArray[$i][$j]=$valuesArray[$i][$j]-($valuesArray[$i][$minCol]*$valuesArray[$minRow][$j]);
                        $valuesArray[$i][$minCol]=0;
                    }
                }

                //display the table per iteration
                echo "<table border='1' id='myTable'>";
                    for ($th=1; $th <= $num[0] ; $th++) {
                    echo "<th class='text-center'>X".$th."</th>";
                }
                for ($s=1; $s <= $num[1] ; $s++) {
                    echo "<th class='text-center'>S".$s."</th>";
                }
                echo "<th class='text-center'>Z</th>";
                echo "<th class='text-center'>Nilai Kanan</th>";
                for($i=0; $i<=$num[1]; $i++)
                {
                    echo "<tr>";
                    for($j=0; $j<=$n+1; $j++)
                        echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<br />";

                //display the basic solution per iteration
                echo "<p class='text-center'>Nilai Kanan:</p><br /><br />";
                echo "<table border='1' id='myTable'>
                <tr>";
                for($i=0; $i<=$n; $i++)
                {
                    $ctr=0;
                    for($j=0; $j<=$num[1]; $j++)
                    {
                        if($valuesArray[$j][$i]!=0)
                        {
                            if($ctr==0 && $valuesArray[$j][$i]==1)
                            {
                                $index=$j;
                                $ctr++;
                            }
                            else
                                break;
                        }
                    }
                    if($ctr==1)
                    {
                        if(($i+1)<=$num[0])
                        {
                            $sub=$i+1;
                            echo "<td id='each'> x".$sub." = ".$valuesArray[$index][$n+1]."</td>";
                        }
                        else
                        {
                            $sub=$i-$num[0]+1;
                            if($sub<=$num[1])
                                echo "<td id='each'> s".$sub." = ".$valuesArray[$index][$n+1]."</td>";
                            else
                                echo "<td id='each'> z = ".$valuesArray[$index][$n+1]."</td>";
                        }
                    }
                    else
                    {
                        if(($i+1)<=$num[0])
                        {
                            $sub=$i+1;
                            echo "<td id='each'> x".$sub." = 0</td>";
                        }
                        else
                        {
                            $sub=$i-$num[0]+1;
                            if($sub<=$num[1])
                                echo "<td id='each'> s".$sub." = 0</td>";
                            else
                                echo "<td id='each'> z = 0</td>";
                        }
                    }
                }
                echo "</tr>
                </table>";
                echo "<br />";
            }
            //if the number of iterations reaches 100, the problem is infeasible
            if($max==100)
                echo "<p class='final'>Masalah tidak bisa dihitung </p>";
            if($minRow!=$n && $flag!=1)
            {
                //display the final table
                echo "<p class='final'>Tabel Hasil: Iterasi ke ".$itr." </p>";
                echo "<table border='1' id='myTable'>";
                    for ($th=1; $th <= $num[0] ; $th++) {
                    echo "<th class='text-center'>X".$th."</th>";
                }
                for ($s=1; $s <= $num[1] ; $s++) {
                    echo "<th class='text-center'>S".$s."</th>";
                }
                echo "<th class='text-center'>Z</th>";
                echo "<th class='text-center'>Nilai Kanan</th>";
                for($i=0; $i<=$num[1]; $i++)
                {
                    echo "<tr>";
                    for($j=0; $j<=$n+1; $j++)
                        echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<br />";

                //display the final basic solution
                echo "<p class='final'>Solusi Nilai Kanan:</p>";
                echo "<table border='1' id='myTable'>
                <tr>";
                for($i=0; $i<=$n; $i++)
                {
                    $ctr=0;
                    for($j=0; $j<=$num[1]; $j++)
                    {
                        if($valuesArray[$j][$i]!=0)
                        {
                            if($ctr==0 && $valuesArray[$j][$i]==1)
                            {
                                $index=$j;
                                $ctr++;
                            }
                            else
                                break;
                        }
                    }
                    if($ctr==1)
                    {
                        if(($i+1)<=$num[0])
                        {
                            $sub=$i+1;
                            echo "<td id='each'> x".$sub." = ".$valuesArray[$index][$n+1]."</td>";
                        }
                        else
                        {
                            $sub=$i-$num[0]+1;
                            if($sub<=$num[1])
                                echo "<td id='each'> s".$sub." = ".$valuesArray[$index][$n+1]."</td>";
                            else
                                echo "<td id='each'> z = ".$valuesArray[$index][$n+1]."</td>";
                        }
                    }
                    else
                    {
                        if(($i+1)<=$num[0])
                        {
                            $sub=$i+1;
                            echo "<td id='each'> x".$sub." = 0</td>";
                        }
                        else
                        {
                            $sub=$i-$num[0]+1;
                            if($sub<=$num[1])
                                echo "<td id='each'> s".$sub." = 0</td>";
                            else
                                echo "<td id='each'> z = 0</td>";
                        }
                    }
                }
                echo "</tr>
                </table>";
                echo "<br />";
            }
        }
        // else
        // {
        //     $flag=0;
        //     $minRow=0;
        //     //solves for the minimization problem
        //     //get the number of constraints from the hidden form input named numbers and put it into the array named num
        //     //$num[0]=num of variables and $num[1]=num of constraints
        //     $num = array();
        //     $i=0;
        //     $token=strtok($_POST['numbers'], ',');
        //     while($token)
        //     {
        //         $num[$i++]=$token;
        //         $token=strtok(',');
        //     }

        //     //$n+1 would be the total number of columns
        //     $n=$num[0]+$num[1];

        //     //get the values from the form and insert it into the array
        //     $valuesArray = array();
        //     for($k=1; $k<=$num[1]+1; $k++)
        //     {
        //         for($j=1; $j<=$num[0]; $j++)
        //         {
        //             if($k!=$num[1]+1)
        //                 $valuesArray[$k-1][$j-1]=floatval($_POST["kendala$k$j"]);
        //             else
        //                 $valuesArray[$k-1][$j-1]=floatval($_POST["variabel$j"]);
        //             if($j==$num[0])
        //             {
        //                 if($k!=$num[1]+1)
        //                     $valuesArray[$k-1][$j]=floatval($_POST["SA$k"]);
        //                 else
        //                     $valuesArray[$k-1][$j]=0;
        //             }
        //         }
        //     }

        //     //if the relational operator is <=, negate the entire row - it's like converting <= to >=
        //     for($k=1; $k<=$num[1]; $k++)
        //     {
        //         for($j=1; $j<=$num[0]; $j++)
        //         {
        //             if($_POST["tanda$k"]=='kurangDari')
        //             {
        //                 if($k!=$num[1]+1 && $valuesArray[$k-1][$j-1]!=0)
        //                     $valuesArray[$k-1][$j-1]*=-1;
        //                 else
        //                     $valuesArray[$k-1][$j-1]*=1;
        //                 if($j==$num[0])
        //                 {
        //                     if($k!=$num[1]+1)
        //                         $valuesArray[$k-1][$j]*=-1;
        //                     else
        //                         $valuesArray[$k-1][$j]=0;
        //                 }
        //             }
        //         }
        //     }

        //     //transpose the matrix because we would be forming the dual problem, we are going to convert the minimization problem to a maximization one
        //     $manager=new simplexMethod;
        //     $valuesArray=$manager->transposeMatrix($valuesArray, $num[1], $num[0]);

        //     //after transposing the matrix, negate the last row
        //     for($i=0; $i<$num[1]; $i++)
        //         $valuesArray[$num[0]][$i]*=-1;

        //     //add the slack variables, the number of slack variables would be the number of unknown variables
        //     for($i=1; $i<=$num[0]+1; $i++)
        //     {
        //         $k=0;
        //         for($j=$num[1]; $j<=$n; $j++)
        //         {
        //             if($k!=($i-1))
        //                 $valuesArray[$i-1][$j]=0;
        //             else if ($i==$num[1]+1)
        //                 $valuesArray[$i-1][$j]=1;
        //             else
        //                 $valuesArray[$i-1][$j]=1;
        //             $k++;
        //         }
        //     }

        //     //add the the coefficients of the objective function to the last column of the array
        //     for($i=1; $i<=$num[0]+1; $i++)
        //     {
        //         if(($i-1)!=$num[0])
        //             $valuesArray[$i-1][$n+1]=floatval($_POST["variabel$i"]);
        //         else
        //             $valuesArray[$i-1][$n+1]=0;
        //     }

        //     //display the initial tableau
        //     echo"<br />Tabel Awal: <br /><br />";
        //     echo "<table border='1' id='myTable'>";
        //     for($i=0; $i<=$num[0]; $i++)
        //     {
        //         echo "<tr>";
        //         for($j=0; $j<=$n+1; $j++)
        //             echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
        //         echo "</tr>";
        //     }
        //     echo "</table>";
        //     echo "<br />";

        //     //display the basic solution
        //     echo "Nilai Kanan Awal<br /><br />";
        //     echo "<table border='1' id='myTable'>
        //     <tr>";
        //     //in the minimization problem, since we are using the dual, the value of the unknown variables would be the values of the slack variables
        //     for($i=0; $i<=$n; $i++)
        //     {
        //         if(($i+1)<=$num[1])
        //         {
        //             $sub=$i+1;
        //             echo "<td id='each'> y".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
        //         }
        //         else
        //         {
        //             $sub=$i-$num[1]+1;
        //             if($sub<=$num[0])
        //                 echo "<td id='each'> x".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
        //             else
        //                 echo "<td id='each'> z = ".$valuesArray[$num[0]][$n+1]."</td>";
        //         }
        //     }
        //     echo "</tr>
        //     </table>";
        //     echo "<br />";

        //     //the maximum number of iterations would be 100, if it excedes the maximum, the problem is infeasible
        //     for($max=0; $max<100; $max++)
        //     {
        //         //get the index of the column with the smallest value
        //         $minCol=$manager->getMinimumColumn($valuesArray, $num[0], $n);
        //         //if there are no negative values, the problem is already minimized
        //         if($minCol==$n+2)
        //             break;

        //         //get the index of the row with the smallest ratio a/b -> a is the rightmost column and b is the positive entry from the minCol
        //         $minRow=$manager->getMinimumRow($valuesArray, $num[0], $minCol, $n);
        //         //if there are no non-negative or zero, the problem is infeasible
        //         if($minRow==$num[0])
        //         {
        //             $flag=1;
        //             echo "<p class='final'>Problem is infeasible. </p>";
        //             break;
        //         }

        //         //display the iteration number
        //         $itr=$max+1;
        //         echo "<br />Iteration number: ".$itr."<br /><br />";

        //         //normalize the pivot row
        //         //divide the pivot row by the pivot element
        //         for($i=0; $i<=$n+1; $i++)
        //             if($i!=$minCol)
        //                 $valuesArray[$minRow][$i]=$valuesArray[$minRow][$i]/$valuesArray[$minRow][$minCol];
        //         $valuesArray[$minRow][$minCol]=1;

        //         //make the rest of the elements of the pivot column 0
        //         for($i=0; $i<=$num[0]; $i++)
        //         {
        //             if($i!=$minRow)
        //             {
        //                 for($j=0; $j<=$n+1; $j++)
        //                     if($j!=$minCol)
        //                         $valuesArray[$i][$j]=$valuesArray[$i][$j]-($valuesArray[$i][$minCol]*$valuesArray[$minRow][$j]);
        //                 $valuesArray[$i][$minCol]=0;
        //             }
        //         }

        //         //display the table per iteration
        //         echo "<table border='1' id='myTable'>";
        //         for($i=0; $i<=$num[0]; $i++)
        //         {
        //             echo "<tr>";
        //             for($j=0; $j<=$n+1; $j++)
        //                 echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
        //             echo "</tr>";
        //         }
        //         echo "</table>";
        //         echo "<br />";

        //         //display the basic solution per iteration
        //         echo "Basic Solution:<br /><br />";
        //         echo "<table border='1' id='myTable'>
        //         <tr>";
        //         for($i=0; $i<=$n; $i++)
        //         {
        //             if(($i+1)<=$num[1])
        //             {
        //                 $sub=$i+1;
        //                 echo "<td id='each'> y".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
        //             }
        //             else
        //             {
        //                 $sub=$i-$num[1]+1;
        //                 if($sub<=$num[0])
        //                     echo "<td id='each'> x".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
        //                 else
        //                     echo "<td id='each'> z = ".$valuesArray[$num[0]][$n+1]."</td>";
        //             }
        //         }
        //         echo "</tr>
        //         </table>";
        //         echo "<br />";
        //     }
        //     //if the number of iterations reaches 100, the problem is infeasible
        //     if($max==100)
        //         echo "<p class='final'>Problem is infeasible. </p>";
        //     if($minRow!=$n && $flag!=1)
        //     {
        //         //display the final table
        //         echo "<p class='final'>Final Tableau: </p>";
        //         echo "<table border='1' id='myTable'>";
        //         for($i=0; $i<=$num[0]; $i++)
        //         {
        //             echo "<tr>";
        //             for($j=0; $j<=$n+1; $j++)
        //                 echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
        //             echo "</tr>";
        //         }
        //         echo "</table>";
        //         echo "<br />";

        //         //display the final basic solution
        //         echo "<p class='final'>Final Basic Solution:</p>";
        //         echo "<table border='1' id='myTable'>
        //         <tr>";
        //         for($i=0; $i<=$n; $i++)
        //         {
        //             if(($i+1)<=$num[1])
        //             {
        //                 $sub=$i+1;
        //                 echo "<td id='each'> y".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
        //             }
        //             else
        //             {
        //                 $sub=$i-$num[1]+1;
        //                 if($sub<=$num[0])
        //                     echo "<td id='each'> x".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
        //                 else
        //                     echo "<td id='each'> z = ".$valuesArray[$num[0]][$n+1]."</td>";
        //             }
        //         }
        //         echo "</tr>
        //         </table>";
        //         echo "<br />";
        //     }
        // }
    }
?>
</div>
@endsection

