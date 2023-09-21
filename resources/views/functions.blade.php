@php
// Mencari hitungKombinasi untuk menghasilkan persamaan dasar
function hitungKombinasi($n, $m) {
    if ($m == 0 || $m == $n) {
        return 1;
    } elseif ($m > $n) {
        return 0;
    } else {
        return hitungKombinasi($n - 1, $m - 1) + hitungKombinasi($n - 1, $m);
    }
}

// Fungsi untuk menyelesaikan sistem persamaan linear dengan metode Gauss-Jordan
function gaussJordan($A, $B) {
    $n = count($A);
    for ($i = 0; $i < $n; $i++) {
        $A[$i][] = $B[$i];
    }

    for ($i = 0; $i < $n; $i++) {
        $maxRow = $i;
        for ($k = $i + 1; $k < $n; $k++) {
            if (abs($A[$k][$i]) > abs($A[$maxRow][$i])) {
                $maxRow = $k;
            }
        }

        list($A[$i], $A[$maxRow]) = array($A[$maxRow], $A[$i]);

        for ($k = $i + 1; $k < $n; $k++) {
            $factor = $A[$k][$i] / $A[$i][$i];
            for ($j = $i; $j < $n + 1; $j++) {
                $A[$k][$j] -= $factor * $A[$i][$j];
            }
        }
    }

    for ($i = $n - 1; $i >= 0; $i--) {
        $sol[$i] = $A[$i][$n] / $A[$i][$i];
        for ($k = $i - 1; $k >= 0; $k--) {
            $A[$k][$n] -= $A[$k][$i] * $sol[$i];
        }
    }

    return $sol;
}



@endphp
