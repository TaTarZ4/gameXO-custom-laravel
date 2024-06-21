<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="{{ asset('me.jpg') }}" />
    <title>Game X O</title>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link href="{{ asset('main.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <section id="home-page">
        <h1>Game X O</h1>
        <h2 id="play" onclick="playPage()">Play</h2>
        <h2 id="play-with-bot" onclick="playWithBotPage()">Play with bot</h2>
        <h2 id="history" onclick="historyPage()">History</h2>
        <h2 id="setting" onclick="settingPage()">Setting</h2>
    </section>

    <section id="setting-page">
        <h1 style="text-align: center;">Setting</h1>
        <div class="display">
            <table class="table-show""></table>
        </div>
        <h2>setting</h2>
        <h4><span>Rows(X) </span><input type="number" min="3" value="3" onchange="changeX(this)"></h4>
        <h4><span>Columns(Y) </span><input type="number" min="3" value="3" onchange="changeY(this)"></h4>
        <h4><span>Condition for win </span><input type="number" min="3" value="3" onchange="changeConditionWinner(this)"></h4>
        <div class="action">
            <button onclick="homePage()"><- Back</button>
        </div>
    </section>
    
    <section id="play-page">
        <h1 style="text-align: center;">Game Start</h1>
        <div class="display">
            <table class="game-start"></table>
        </div>
        <div class="notice">
            <div class="showSymbol"></div><h3 class="notice-text">is winner</h3>
        </div>
        <div class="action">
            <button onclick="homePage(),resetGame()"><- Back</button>
            <button class="btn-new-game" onclick="resetGame()">New game</button>
        </div>
    </section>

    <section id="history-page">
        <h1 style="text-align: center;">History</h1>
        <div id="show-replay">
            <div class="display">
                <table id="table-show-replay"></table>
            </div>
        </div>
        <div>
            <table id="list-history"></table>
        </div>
        <div class="action">
            <button onclick="homePage()"><- Back</button>
        </div>
    </section>
</body>
<script>
    let qtyX = 3;
    let qtyY = 3;
    let qtyWin = 3;
    let stepMake = [];
    let gameFinish = false;
    let winner = '';
    let typeGame = '';

    //setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#play-page .notice').hide();
    $('#play-page .draw').hide();
    $('.btn-new-game').hide();
    //end setup

    //change page
    function homePage(){
        $('#setting-page').hide();
        $('#play-page').hide();
        $('#play-with-bot-page').hide();
        $('#history-page').hide();
        $('#home-page').show();
    }
    homePage();

    function playPage(){
        $('#play-page').show();
        $('#home-page').hide();
        typeGame = 'friend';
        tableGameStart(typeGame);
    }

    function playWithBotPage(){
        $('#play-page').show();
        $('#home-page').hide();
        typeGame = 'bot';
        tableGameStart(typeGame);
    }

    function settingPage(){
        $('#setting-page').show();
        $('#home-page').hide();
    }

    function historyPage(){
        $('#history-page').show();
        $('#home-page').hide();
        getHistory()
    }
    //end change page

    // function for setting page
    function settingTable(){
        let html = '';
        for(let i = 0; i < qtyY; i++){
            let td = '';
            for(let j = 0; j < qtyX; j++){
                td += `<td></td>`
            }
            html += `<tr>${td}</tr>`
        }
        $('.table-show').html(html);
    }
    settingTable();

    function changeX(e){
        qtyX = $(e).val();
        settingTable();
    }

    function changeY(e){
        qtyY = $(e).val();
        settingTable();
    }

    function changeConditionWinner(e){
        qtyWin = $(e).val();
        settingTable();
    }
    //end function for setting page

    //function for game play
    let turn = 1;
    let gamePlay = [];
        // 1 is X , 0 is O
    function tableGameStart(type){
        let html = '';
        let y = [];
        for(let i = 0; i < qtyY; i++){
            let td = '';
            let x = [];
            for(let j = 0; j < qtyX; j++){
                td += `<td data-position="[${j},${i}]"></td>`
                x.push(null);
            }
            html += `<tr>${td}</tr>`
            y.push(x);
        }
        $('.game-start').html(html);
        gamePlay = y;

        $('td').click(function(){
            let position = $(this).data('position');
            if(!gameFinish){
                if(gamePlay[position[1]][position[0]] == null){
                    stepMake.push([position[0],position[1],turn]);
                    if(type == 'bot'){
                        $(this).addClass('actionX');
                        gamePlay[position[1]][position[0]] = 1;
                        turn = 0;
                        checkWinner();
                        botAction();
                    }else{
                        if(turn == 1){
                            $(this).addClass('actionX');
                            gamePlay[position[1]][position[0]] = 1;
                            turn = 0;
                        }else{
                            $(this).addClass('actionO');
                            gamePlay[position[1]][position[0]] = 0;
                            turn = 1;
                        }
                        checkWinner();
                    }
                }
            }
        })
    }

    function checkWinner(){
        let condition1 = true;
        let condition2 = true;
        let condition3 = true;
        let condition4 = true;
        let draw = true;

        for(let i = 0; i < gamePlay.length; i++){
            for(let j = 0; j < gamePlay[i].length; j++){
                if(gamePlay[i][j] !== null){
                    for(let k = 1; k < qtyWin; k++){
                        condition1 = condition1 && gamePlay[i][j] == gamePlay[i][j+k];
                        if(i+k < gamePlay.length){
                            condition2 = condition2 && gamePlay[i][j] == gamePlay[i+k][j+k];
                            condition3 = condition3 && gamePlay[i][j] == gamePlay[i+k][j];
                            condition4 = condition4 && gamePlay[i][j] == gamePlay[i+k][j-k];
                        }else{
                            condition2 = false;
                            condition3 = false;
                            condition4 = false;
                        }
                    }
                    if(condition1 || condition2 || condition3 || condition4){
                        $('#play-page .showSymbol').show();
                        $('#play-page .notice-text').text(' is winner');
                        $('#play-page .notice-text').css("color","green");
                        if(gamePlay[i][j] == 1){
                            if(typeGame == 'bot'){
                                $('#play-page .showSymbol').hide();
                                $('#play-page .notice-text').text('You win');
                                $('#play-page .notice-text').css("color","green");
                                winner = 'you'
                            }else{
                                $('#play-page .showSymbol').removeClass('actionO');
                                $('#play-page .showSymbol').addClass('actionX');
                                winner = 'x';    
                            }
                        }else{
                            if(typeGame == 'bot'){
                                $('#play-page .showSymbol').hide();
                                $('#play-page .notice-text').text('You lost');
                                $('#play-page .notice-text').css("color","red");
                                winner = 'bot'
                            }else{
                                $('#play-page .showSymbol').removeClass('actionX');
                                $('#play-page .showSymbol').addClass('actionO');
                                winner = 'o';    
                            }
                        }
                        $('#play-page .notice').show();
                        $('.btn-new-game').show();
                        saveGame();
                        gameFinish = true;
                        break;
                    }

                    //reset condition
                    condition1 = true;
                    condition2 = true;
                    condition3 = true;
                    condition4 = true;
                }else{
                    draw = false;
                }
            }
        }

        if(draw && gameFinish == false){ 
            $('.btn-new-game').show();
            $('#play-page .notice').show();
            $('#play-page .showSymbol').hide();
            $('#play-page .notice-text').text('Draw');
            $('#play-page .notice-text').css("color","black");
            winner = 'draw';
            gameFinish = true;
            saveGame();
        }
    }

    function resetGame(){
        condition = false;
        stepMake = [];
        gameFinish = false;
        turn = 1;
        gamePlay = [];
        winner = [];
        tableGameStart(typeGame);
        $('#play-page .notice').hide();
        $('#play-page .draw').hide();
        $('.btn-new-game').hide();
    }

    function saveGame(){
        $.ajax({
            type: "POST",
            url: "/save",
            data: {qtyX:qtyX , qtyY:qtyY , qtyWin:qtyWin , winner:winner , type:typeGame , actions:stepMake},
            success:function(res){
                // console.log(res.status)
            }
        });
    }
    //end function for game play
    
    //function for play with bot
    function botAction(){
        let canMake = [];
        let condition1 = true;
        let condition2 = true;
        let condition3 = true;
        let condition4 = true;
        let turnBot = true;

        if(!gameFinish){
            let checkPlayWin = gamePlay.map((iValue , i)=>{
                iValue.map((jValue , j)=>{
                    if(gamePlay[i][j] !== null){
                        // i is Y(columns) and j is X(row)
                        for(let k=1; k < qtyWin-1;k++){
                            condition1 = condition1 && gamePlay[i][j] == gamePlay[i][j+k];
                            if(i+k < gamePlay.length){
                                condition2 = condition2 && gamePlay[i][j] == gamePlay[i+k][j+k];
                                condition3 = condition3 && gamePlay[i][j] == gamePlay[i+k][j];
                                condition4 = condition4 && gamePlay[i][j] == gamePlay[i+k][j-k];
                            }else{
                                condition2 = false;
                                condition3 = false;
                                condition4 = false;
                            }
                        }
                        let x = 0;
                        let y = 0;
                        if(condition1){
                            y=i;x=j-1;
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                            y=i;x=j+qtyWin-1;
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                        }
                        if(condition2){
                            y=i-1;x=j-1;
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                            y=i+qtyWin-1;x=j+qtyWin-1;
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                        }
                        if(condition3){
                            y=i-1;x=j;
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                            y=i+qtyWin-1;x=j;
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                        }
                        if(condition4){
                            y=i-1;x=j+1;
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                            y=i+(qtyWin-1);x=j-(qtyWin-1);
                            if(y < gamePlay.length && y > -1 && x < gamePlay[0].length && x > -1){
                                if(gamePlay[y][x] == null && turnBot){
                                    $(`td[data-position="[${x},${y}]"]`).addClass('actionO');
                                    gamePlay[y][x] = 0;
                                    stepMake.push([x,y,turn]);
                                    turnBot = false;
                                }
                            }
                        }
                        //reset condition
                        condition1 = true;
                        condition2 = true;
                        condition3 = true;
                        condition4 = true;
                    }
                })
            })
            if(turnBot){
                gamePlay.map((i , iIndex)=>{
                    i.map((j , jIndex)=>{
                        // i is Y(columns) and j is X(row)
                        if(j == null){
                            canMake.push([iIndex,jIndex,turn]);
                        }
                    });
                });
                let action = canMake[(Math.floor(Math.random() * canMake.length))];
                $(`td[data-position="[${action[1]},${action[0]}]"]`).addClass('actionO');
                gamePlay[action[0]][action[1]] = 0;
                stepMake.push([action[1],action[0],turn]);
            }
            turn = 1;
            checkWinner();
            // console.log(gamePlay);
        }
    }
    //function for play with bot

    //function for replay history
    let replayStepMake = [];
    let replayRound = [];
    function getHistory(){
        let html = '<tr><th>No</th><th>Type</th><th>Winner</th><th>Date</th><th>Action</th></tr>';
        $.ajax({
            type: "GET",
            url: "/history",
            success: function(res){
                res.data.map((e , index)=>{
                    newDate = new Date(e.updated_at);
                    html += `<tr><td>${index+1}</td><td>${e.type}</td><td>${e.winner}</td><td>${newDate.toLocaleDateString()}</td><td><button class="btn-replay" onclick="getActionsReplay(${e.id})">Replay</button></td></tr>`
                })

                $('#list-history').html(html);
            }
        });
    }

    function getActionsReplay(id){
        $.ajax({
            type: "POST",
            url: "/history/show",
            data: {id:id},
            success:function(res){
                replayStepMake = res.actions;
                replayRound = res.round;
                showTableReplay()
            }
        })
    }

    function showTableReplay(){
        let html = '';

        for(let i = 0; i < replayRound.qtyY; i++){
            let td = '';
            for(let j = 0; j < replayRound.qtyX; j++){
                td += `<td position="${j},${i}"></td>`
            }
            html += `<tr>${td}</tr>`
        }
        $('#table-show-replay').html(html);

        let i = 0;
        function replayStart(){
            setTimeout(() => {
                if(i < replayStepMake.length){
                    let action = replayStepMake[i];
                    $(`#table-show-replay td[position="${action.x},${action.y}"]`).addClass(action.action == 'x'?'actionX':'actionO');
                    replayStart();
                };
                i++;
            }, 1000);
        }
        replayStart();
    }
    //end function for relay history
</script>
</html>