<html>

<head>
    <title>{{ $games->game_name }}</title>
    <style type="text/css">
        html,
        body,
        iframe {
            left: 0px;
            top: 0px;
            position: relative;
            display: flex;
            align-items: center;
            background-color: black;
            margin: 0px;
            padding: 0px;
            width: 100%;
            height: 100%;
            border: 0px;
        }
    </style>
    <script>
        function closeGame(){
window.location.href='/'; }
window.gclose=closeGame;
window.close=closeGame;
window.onmessage=function(event){
if (event.data=='closeGame' || event.data=='close' || event.data.indexOf("GAME_MODE:LOBBY")>=0) {
closeGame(); } }
    </script>
</head>

<body>
    <iframe id="frame" src="{{ $games->url }}" allowfullscreen></iframe>
</body>

</html>
