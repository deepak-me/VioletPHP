<html>
    <head>
        <style>
            .abc
            {
                font-weight: bold;
            }
            
        </style>    
    </head>
    <body>

     <!--   <form action="http://localhost/violetphp/dbController/postTest2" method="POST">
            My Name: <input type="text" name="myname"/>
            <input type="submit" name="submit" value="POST this"/>
        </form>
     -->       



      <b>From Database</b><br/>
   {{loop}}
   Name: <b>{{dbValue.first_name}}</b>  Gender: <b>{{dbValue.gender}}</b>  <br/>
   {{/loop}}
   
   {{loop}}
   <a href="{{navLink}}"> {{navLink}} </a> 
   {{/loop}}
        <hr/>
    
    {{loop}} {{dbValue2.make}} {{/loop}}
      
       
    </body>
</html>