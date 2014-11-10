<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Twi_graph</title>
    </head>
<body>
    <h1>Twitterグラフ</h1>
    <p>過去２０件のTweetを取得します</p>
    <form action="" method="POST">
        <input type="text" name="screen_name" value="" />
        <input type="submit" name="smt" value="click" />
    </form>
    <!-- エラーが発生した場合 -->
    {if $err_msg !== ''}{$err_msg}{/if}
    <canvas id="bar" height="400" width="700"></canvas>

    <!-- Chart.jsの記述部と設定 -->
    <script text="text/javascript" src="./js/Chart.js"></script>
    <script text="text/javascript">
      var barChartData = {
              labels : [
                  {foreach from=$result item=value key=key}
                      "{$key}",
                  {/foreach}
              ],
              datasets : [
                  {
                      fillColor : "rgba(151,187,205,0.5)",
                      strokeColor : "rgba(151,187,205,1)",
                      data : [
                          {foreach from=$result item=value key=key}
                              {$value|floor},
                          {/foreach}
                      ]
                  }]
      }

      var option = {
               //Boolean - If we show the scale above the chart data			
               scaleOverlay : false,

               //Boolean - If we want to override with a hard coded scale
               scaleOverride : false,

               //** Required if scaleOverride is true **
               //Number - The number of steps in a hard coded scale
               scaleSteps : null,
               //Number - The value jump in the hard coded scale
               scaleStepWidth : null,
               //Number - The scale starting value
               scaleStartValue : null,

               //String - Colour of the scale line	
               scaleLineColor : "rgba(0,0,0,.1)",

               //Number - Pixel width of the scale line	
               scaleLineWidth : 1,

               //Boolean - Whether to show labels on the scale	
               scaleShowLabels : true,

               //Interpolated JS string - can access value
               scaleLabel : "<%=value%>",

               //String - Scale label font declaration for the scale label
               scaleFontFamily : "'Arial'",

               //Number - Scale label font size in pixels	
               scaleFontSize : 12,

               //String - Scale label font weight style	
               scaleFontStyle : "normal",

               //String - Scale label font colour	
               scaleFontColor : "#666",	

               ///Boolean - Whether grid lines are shown across the chart
               scaleShowGridLines : true,

               //String - Colour of the grid lines
               scaleGridLineColor : "rgba(0,0,0,.05)",

               //Number - Width of the grid lines
               scaleGridLineWidth : 1,	

               //Boolean - If there is a stroke on each bar	
               barShowStroke : true,

               //Number - Pixel width of the bar stroke	
               barStrokeWidth : 2,

               //Number - Spacing between each of the X value sets
               barValueSpacing : 5,

               //Number - Spacing between data sets within X values
               barDatasetSpacing : 1,

               //Boolean - Whether to animate the chart
               animation : true,

               //Number - Number of animation steps
               animationSteps : 60,

               //String - Animation easing effect
               animationEasing : "easeOutQuart",

               //Function - Fires when the animation is complete
               onAnimationComplete : null
      }

      var myLine = new Chart(document.getElementById("bar").getContext("2d")).Bar(barChartData,option);    
    </script>
</body>
</html>
