<html>
    <head>
        
    </head>
    <body>

     <!--   <form action="http://localhost/violetphp/dbController/postTest2" method="POST">
            My Name: <input type="text" name="myname"/>
            <input type="submit" name="submit" value="POST this"/>
        </form>
     -->       
 {{keyArray.name}}

<hr/>

     {{isset:dbValue2}}
      
   {{loop}}{{dbValue.NAME}} {{dbValue.AGE}} <br/>{{/loop}}
   
        <hr/>
      simple array <br/>
      {{loop}}{{simpleArray}} {{/loop}} <br/>


       {{/isset}}

       
      {{isset:dbValue3}}isset same line{{/isset}}
     
      
       
    </body>
</html>