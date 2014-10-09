<html>
    <head>
        
    </head>
    <body>

     <!--   <form action="http://localhost/violetphp/dbController/postTest2" method="POST">
            My Name: <input type="text" name="myname"/>
            <input type="submit" name="submit" value="POST this"/>
        </form>
     -->       
     {{loop}}
     {{dbValue.id}} {{dbValue.name}} {{dbValue.price}} <br/>
     {{/loop}}
    </body>
</html>