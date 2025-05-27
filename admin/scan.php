<script type="text/javascript">
    $(document).ready(function(){
        setInterval(function(){
            $("#cekkartu").load('bacakartu.php')
        }, 1000 );
    });
</script>

<div class="shadow-lg rounded-lg bg-white p-3 w-[83%] h-auto ml-64 mt-16">
 <div id="cekkartu"></div>   
</div>
