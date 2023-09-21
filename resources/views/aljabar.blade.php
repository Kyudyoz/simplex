@extends('layouts.main')
@section('section')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/style.css') }}" />

<div>
    <h2 class="heading">Hasil Perhitungan</h2>
</div>
<div class="container bg-dark text-white">
    <p><a href="/" class="btn btn-light mt-4">Ulang</a></p>





    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

    <canvas id="feasibleRegion" width="400" height="400"></canvas>

    <script>
        // Define the canvas and context
        var canvas = document.getElementById('feasibleRegion');
        var ctx = canvas.getContext('2d');

        // Define the objective function coefficients
        var objectiveCoefficients = [2, 3];

        // Define the constraint coefficients and RHS values
        var constraintCoefficients = [
            [1, 2],
            [3, 1],
            [-1, 1]
        ];
        var rhsValues = [10, 9, 5];

        // Calculate the coordinates of the feasible region
        var xMax = 100; // Adjust as needed
        var yMax = 100; // Adjust as needed
        var xStep = 1; // Adjust as needed
        var yStep = 1; // Adjust as needed
        var feasibleRegion = [];

        for (var x = 0; x <= xMax; x += xStep) {
            for (var y = 0; y <= yMax; y += yStep) {
                var constraintsSatisfied = true;
                for (var i = 0; i < constraintCoefficients.length; i++) {
                    var lhs = constraintCoefficients[i][0] * x + constraintCoefficients[i][1] * y;
                    if (lhs > rhsValues[i]) {
                        constraintsSatisfied = false;
                        break;
                    }
                }
                if (constraintsSatisfied) {
                    feasibleRegion.push({ x: x, y: y });
                }
            }
        }

        // Draw the Cartesian plane
        ctx.beginPath();
        ctx.moveTo(0, 0);
        ctx.lineTo(canvas.width, 0);
        ctx.moveTo(0, 0);
        ctx.lineTo(0, canvas.height);
        ctx.stroke();

        // Draw the feasible region
        ctx.beginPath();
        for (var i = 0; i < feasibleRegion.length; i++) {
            var x = feasibleRegion[i].x * 20; // Scale for display
            var y = canvas.height - feasibleRegion[i].y * 20; // Invert y-axis and scale
            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.closePath();
        ctx.fillStyle = 'rgba(0, 0, 255, 0.5)';
        ctx.fill();
        ctx.stroke();
    </script>




</div>
@endsection
