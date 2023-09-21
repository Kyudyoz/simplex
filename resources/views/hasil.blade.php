@extends('layouts.main')
@section('section')
@include('library')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/style.css') }}" />

<div>
    <h2 class="heading">Hasil Perhitungan</h2>
</div>
<div class="container bg-dark text-white">
    <p><a href="/" class="btn btn-light mt-4">Ulang</a></p>
@php

            if($_POST['category']=='max')
            {
                $flag=0;
                $minRow=0;
                $num = array();
                $i=0;
                $token=strtok($_POST['numbers'], ',');
                while($token)
                {
                    $num[$i++]=$token;
                    $token=strtok(',');
                }
                $n=$num[0]+$num[1];
                $valuesArray = array();
                for($k=1; $k<=$num[1]+1; $k++)
                {
                    for($j=1; $j<=$num[0]; $j++)
                    {
                        if($k!=$num[1]+1)
                            $valuesArray[$k-1][$j-1]=floatval($_POST["kendala$k$j"]);
                        else
                            $valuesArray[$k-1][$j-1]=floatval($_POST["variabel$j"])*-1;
                    }
                }
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
                            if($_POST["tanda$i"]=="kurangDari")
                                $valuesArray[$i-1][$j]=1;
                            else
                                $valuesArray[$i-1][$j]=-1;
                        }
                        $k++;
                    }
                }
                for($i=1; $i<=$num[1]+1; $i++)
                {
                    if($i!=$num[1]+1)
                        $valuesArray[$i-1][$n+1]=floatval($_POST["SA$i"]);
                    else
                        $valuesArray[$i-1][$n+1]=0;
                }
                echo"<br /><p class='text-center'>Tabel Awal:</p>";
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
                echo "<p class='text-center'>Nilai Kanan Awal:</p>";
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
                $manager=new simplexMethod;
                for($max=0; $max<100; $max++)
                {
                    $minCol=$manager->getMinimumColumn($valuesArray, $num[1], $n);
                    if($minCol==$n+2)
                        break;
                    $minRow=$manager->getMinimumRow($valuesArray, $num[1], $minCol, $n);
                    if($minRow==$num[1])
                    {
                        $flag=1;
                        echo "<p class='final'>Masalah tidak bisa dihitung </p>";
                        break;
                    }
                    $itr=$max+1;
                    echo "<br /><p class='text-center'>Iterasi ke-".$itr.":</p>";
                    for($i=0; $i<=$n+1; $i++)
                        if($i!=$minCol)
                            $valuesArray[$minRow][$i]=$valuesArray[$minRow][$i]/$valuesArray[$minRow][$minCol];
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
                    echo "<p class='text-center'>Nilai Kanan:</p>";
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
                if($max==100)
                    echo "<p class='final'>Masalah tidak bisa dihitung </p>";
                if($minRow!=$n && $flag!=1)
                {
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
                    echo "<p class='final'>Solusi Nilai Kanan</p>";
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
            else
            {
                $flag=0;
                $minRow=0;
                $num = array();
                $i=0;
                $token=strtok($_POST['numbers'], ',');
                while($token)
                {
                    $num[$i++]=$token;
                    $token=strtok(',');
                }
                $n=$num[0]+$num[1];
                $valuesArray = array();
                for($k=1; $k<=$num[1]+1; $k++)
                {
                    for($j=1; $j<=$num[0]; $j++)
                    {
                        if($k!=$num[1]+1)
                            $valuesArray[$k-1][$j-1]=floatval($_POST["kendala$k$j"]);
                        else
                            $valuesArray[$k-1][$j-1]=floatval($_POST["variabel$j"]);
                        if($j==$num[0])
                        {
                            if($k!=$num[1]+1)
                                $valuesArray[$k-1][$j]=floatval($_POST["SA$k"]);
                            else
                                $valuesArray[$k-1][$j]=0;
                        }
                    }
                }
                for($k=1; $k<=$num[1]; $k++)
                {
                    for($j=1; $j<=$num[0]; $j++)
                    {
                        if($_POST["tanda$k"]=='kurangDari')
                        {
                            if($k!=$num[1]+1 && $valuesArray[$k-1][$j-1]!=0)
                                $valuesArray[$k-1][$j-1]*=-1;
                            else
                                $valuesArray[$k-1][$j-1]*=1;
                            if($j==$num[0])
                            {
                                if($k!=$num[1]+1)
                                    $valuesArray[$k-1][$j]*=-1;
                                else
                                    $valuesArray[$k-1][$j]=0;
                            }
                        }
                    }
                }
                $manager=new simplexMethod;
                $valuesArray=$manager->transposeMatrix($valuesArray, $num[1], $num[0]);
                for($i=0; $i<$num[1]; $i++)
                    $valuesArray[$num[0]][$i]*=-1;
                for($i=1; $i<=$num[0]+1; $i++)
                {
                    $k=0;
                    for($j=$num[1]; $j<=$n; $j++)
                    {
                        if($k!=($i-1))
                            $valuesArray[$i-1][$j]=0;
                        else if ($i==$num[1]+1)
                            $valuesArray[$i-1][$j]=1;
                        else
                            $valuesArray[$i-1][$j]=1;
                        $k++;
                    }
                }
                for($i=1; $i<=$num[0]+1; $i++)
                {
                    if(($i-1)!=$num[0])
                        $valuesArray[$i-1][$n+1]=floatval($_POST["variabel$i"]);
                    else
                        $valuesArray[$i-1][$n+1]=0;
                }
                echo"<br /> <p class='text-center'>Tabel Awal</p>";
                echo "<table border='1' id='myTable'>";
                    for ($th=1; $th <= $num[0] ; $th++) {
                        echo "<th class='text-center'>X".$th."</th>";
                    }
                    for ($s=1; $s <= $num[1] ; $s++) {
                        echo "<th class='text-center'>S".$s."</th>";
                    }
                    echo "<th class='text-center'>Z</th>";
                    echo "<th class='text-center'>Nilai Kanan</th>";
                for($i=0; $i<=$num[0]; $i++)
                {
                    echo "<tr>";
                    for($j=0; $j<=$n+1; $j++)
                        echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<br />";
                echo "<p class='text-center'>Nilai kanan awal</p>";
                echo "<table border='1' id='myTable'>
                <tr>";
                for($i=0; $i<=$n; $i++)
                {
                    if(($i+1)<=$num[1])
                    {
                        $sub=$i+1;
                        echo "<td id='each'> x".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
                    }
                    else
                    {
                        $sub=$i-$num[1]+1;
                        if($sub<=$num[0])
                            echo "<td id='each'> s".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
                        else
                            echo "<td id='each'> z = ".$valuesArray[$num[0]][$n+1]."</td>";
                    }
                }
                echo "</tr>
                </table>";
                echo "<br />";
                for($max=0; $max<100; $max++)
                {
                    $minCol=$manager->getMinimumColumn($valuesArray, $num[0], $n);
                    if($minCol==$n+2)
                        break;
                    $minRow=$manager->getMinimumRow($valuesArray, $num[0], $minCol, $n);
                    if($minRow==$num[0])
                    {
                        $flag=1;
                        echo "<p class='final'>Masalah tidak bisa dihitung </p>";
                        break;
                    }
                    $itr=$max+1;
                    echo "<br /><p class='text-center'>Iterasi ke-".$itr.":</p>";
                    for($i=0; $i<=$n+1; $i++)
                        if($i!=$minCol)
                            $valuesArray[$minRow][$i]=$valuesArray[$minRow][$i]/$valuesArray[$minRow][$minCol];
                    $valuesArray[$minRow][$minCol]=1;
                    for($i=0; $i<=$num[0]; $i++)
                    {
                        if($i!=$minRow)
                        {
                            for($j=0; $j<=$n+1; $j++)
                                if($j!=$minCol)
                                    $valuesArray[$i][$j]=$valuesArray[$i][$j]-($valuesArray[$i][$minCol]*$valuesArray[$minRow][$j]);
                            $valuesArray[$i][$minCol]=0;
                        }
                    }
                    echo "<table border='1' id='myTable'>";
                        for ($th=1; $th <= $num[0] ; $th++) {
                        echo "<th class='text-center'>X".$th."</th>";
                    }
                    for ($s=1; $s <= $num[1] ; $s++) {
                        echo "<th class='text-center'>S".$s."</th>";
                    }
                    echo "<th class='text-center'>Z</th>";
                    echo "<th class='text-center'>Nilai Kanan</th>";
                    for($i=0; $i<=$num[0]; $i++)
                    {
                        echo "<tr>";
                        for($j=0; $j<=$n+1; $j++)
                            echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "<br />";
                    echo "<p class='text-center'>Nilai kanan</p>";
                    echo "<table border='1' id='myTable'>
                    <tr>";
                    for($i=0; $i<=$n; $i++)
                    {
                        if(($i+1)<=$num[1])
                        {
                            $sub=$i+1;
                            echo "<td id='each'> x".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
                        }
                        else
                        {
                            $sub=$i-$num[1]+1;
                            if($sub<=$num[0])
                                echo "<td id='each'> s".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
                            else
                                echo "<td id='each'> z = ".$valuesArray[$num[0]][$n+1]."</td>";
                        }
                    }
                    echo "</tr>
                    </table>";
                    echo "<br />";
                }
                if($max==100)
                    echo "<p class='final'>Masalah tidak bisa dihitung. </p>";
                if($minRow!=$n && $flag!=1)
                {
                    echo "<p class='final'>Tabel hasil: Iterasi ke-".$itr." </p>";
                    echo "<table border='1' id='myTable'>";
                        for ($th=1; $th <= $num[0] ; $th++) {
                        echo "<th class='text-center'>X".$th."</th>";
                    }
                    for ($s=1; $s <= $num[1] ; $s++) {
                        echo "<th class='text-center'>S".$s."</th>";
                    }
                    echo "<th class='text-center'>Z</th>";
                    echo "<th class='text-center'>Nilai Kanan</th>";
                    for($i=0; $i<=$num[0]; $i++)
                    {
                        echo "<tr>";
                        for($j=0; $j<=$n+1; $j++)
                            echo "<td id='each'>".$valuesArray[$i][$j]."</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "<br />";
                    echo "<p class='final'>Solusi nilai kanan</p>";
                    echo "<table border='1' id='myTable'>
                    <tr>";
                    for($i=0; $i<=$n; $i++)
                    {
                        if(($i+1)<=$num[1])
                        {
                            $sub=$i+1;
                            echo "<td id='each'> x".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
                        }
                        else
                        {
                            $sub=$i-$num[1]+1;
                            if($sub<=$num[0])
                                echo "<td id='each'> s".$sub." = ".$valuesArray[$num[0]][$i]."</td>";
                            else
                                echo "<td id='each'> z = ".$valuesArray[$num[0]][$n+1]."</td>";
                        }
                    }
                    echo "</tr>
                    </table>";
                    echo "<br />";
                }
            }

@endphp
</div>
@endsection
