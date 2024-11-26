var Util = require("../../../utils/slot_utils");

function SlotMachine() {
    // 머신의 상태
    this.spinIndex = 0;
    this.currentGame = "BASE";
    this.gameSort = "BASE";
    this.lineCount = 20;
    this.freeSpinCount = 10;
    // 서버에게 되돌리는 변수
    this.view = [];
    this.virtualReels = {};
    this.winMoney = 0;
    this.winLines = [];
    this.scatterWin = 0;
    this.scatterPositions = [];
    // 프리스핀 연관 변수
    this.freeSpinIndex = 0;
    this.freeSpinLength = 0;
    this.freeSpinBeforeMoney = 0;
    this.freeSpinWinMoney = 0;
    this.freeSpinCacheList = [];
    // 슈퍼보너스 연관 변수
    this.moneyBonusWin = 0;
    this.superBonusCacheIndex = 0;
    this.superBonusCache = {};
    this.superBonusCacheList = [];
    this.superBonusCount = 0;
    this.superBonusMax = 0;

    //디비내역이 아님
    this.patternCount = 2000; //생성패턴갯수
    this.lowLimit = 10; //패턴갯수 위험한계
    this.prevBalance = 0; //배팅전 유저금액 (이력저장할때 이용)

    this.betPerLine = 0;
    this.totalBet = 0;
    this.jackpotType = ["FREE", "BONUS"];
};

var scatter = 1;
var wild = 2;
var wildInFree = 14;
var superSymbol = 3;
var emptySymbol = 13;
var slotWidth = 5;
var slotHeight = 3;
var baseReels = [
    [5, 7, 3, 3, 3, 11, 11, 4, 9, 2, 2, 10, 7, 8, 9, 4, 10, 5, 9, 11, 10, 7, 5, 6, 8, 3, 10, 10, 9],
    [11, 8, 10, 3, 3, 3, 11, 9, 11, 4, 7, 4, 2, 2, 7, 10, 10, 11, 1, 8, 6, 8, 11, 3, 5, 6, 3],
    [4, 10, 3, 3, 3, 11, 9, 10, 6, 7, 11, 7, 2, 2, 8, 4, 8, 3, 6, 9, 10, 9, 1, 5, 2, 2, 3, 5, 9, 2, 2, 8, 8],
    [8, 7, 7, 9, 9, 9, 2, 2, 11, 10, 1, 9, 6, 5, 4, 5, 8, 8, 10, 4, 3, 3, 3, 11, 11, 6, 2, 2, 11],
    [8, 4, 2, 2, 8, 7, 6, 3, 3, 3, 11, 10, 8, 11, 4, 10, 11, 9, 3, 10, 5, 10, 9, 6, 9, 9, 7, 5, 7],
];
var freeReels = [
    [9, 10, 10, 11, 2, 2, 9, 10, 14, 14, 9, 11, 8, 5, 9, 5, 11, 9, 7, 6, 7, 14, 14, 7, 10, 4],
    [8, 8, 14, 14, 11, 11, 5, 4, 6, 14, 14, 11, 5, 7, 10, 10, 6, 11, 10, 4, 9, 2, 2, 9],
    [8, 2, 2, 14, 14, 10, 7, 9, 11, 8, 10, 9, 14, 14, 9, 7, 4, 6, 6, 5, 7, 10],
    [2, 2, 14, 14, 11, 6, 7, 4, 5, 10, 14, 14, 6, 11, 9, 8, 7, 8, 9, 8, 4, 6, 10],
    [11, 14, 14, 7, 9, 11, 7, 5, 5, 4, 9, 9, 10, 6, 2, 2, 14, 14, 7, 11, 8, 6, 8, 10, 9],
];
var payTable = [
    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 4, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 50, 50, 15, 10, 10, 10, 5, 5, 5, 5, 0, 0, 0],
    [0, 0, 150, 150, 50, 40, 30, 25, 10, 10, 10, 10, 0, 0, 0],
    [0, 0, 500, 400, 200, 150, 100, 50, 25, 20, 20, 20, 0, 0, 0],
];
var payLines = [
    [5, 6, 7, 8, 9], // 1
    [0, 1, 2, 3, 4], // 2
    [10, 11, 12, 13, 14], // 3
    [0, 6, 12, 8, 4], // 4
    [10, 6, 2, 8, 14], // 5
    [5, 1, 2, 3, 9], // 6
    [5, 11, 12, 13, 9], // 7
    [0, 1, 7, 13, 14], // 8
    [10, 11, 7, 3, 4], // 9
    [5, 11, 7, 3, 9], // 10
    [5, 1, 7, 13, 9], // 11
    [0, 6, 7, 8, 4], // 12
    [10, 6, 7, 8, 14], // 13
    [0, 6, 2, 8, 4], // 14
    [10, 6, 12, 8, 14], // 15
    [5, 6, 2, 8, 9], // 16
    [5, 6, 12, 8, 9], // 17
    [0, 1, 12, 3, 4], // 18
    [10, 11, 2, 13, 14], // 19
    [0, 11, 12, 13, 4], // 20
];

SlotMachine.prototype.Init = function () {
    this.highPercent = 1; //(0-5)고배당 출현확율 (그래프 굴곡이 심해진다.),
    this.normalPercent = 20; // 베이스 일반금액 퍼센트, 이것이 줄면 프리가 높고 많아지고, 늘면 프리가 낮고 적어진다.
};

SlotMachine.prototype.SpinFromPattern = function (player) {
    this.gameSort = this.currentGame;

    this.totalBet = player.totalBet;
    this.betPerLine = player.betPerLine;

    this.winMoney = 0;
    this.winLines = [];

    if (this.currentGame == "FREE") {
        this.FreeSpin(player);
        return;
    }

    var viewCache = player.viewCache;

    if (viewCache.type == "BASE") {
        this.view = viewCache.view;
    } else if (viewCache.type == "FREE") {
        var cache = viewCache.view;
        this.freeSpinCacheList = cache.viewList;
        this.freeSpinLength = cache.length;
        this.view = this.freeSpinCacheList[0];
        this.winLines = WinLinesFromView(this.view);
        var freeSpinMoney = (viewCache.win / viewCache.bpl) * player.betPerLine;
        // console.log(`[프리스핀] ${freeSpinMoney}`);
    } else if (viewCache.type == "BONUS") {
        // var cache = viewCache.view;
        this.superBonusCacheList = viewCache.view;

        var cache = this.superBonusCacheList[0];
        this.view = cache.view;
        this.superBonusCount = cache.count;
        this.superBonusMax = cache.max;

        var lastView = this.superBonusCacheList[this.superBonusCacheList.length - 1].view;
        console.log(`[보너스스핀] ${WinFromView(lastView, player.betPerLine) + WinFromView(this.view, player.betPerLine)}`);
    }

    this.winMoney = WinFromView(this.view, player.betPerLine);
    this.scatterWin = ScatterWinFromView(this.view, Number(player.betPerLine * this.lineCount));
    this.winMoney += this.scatterWin;

    this.winLines = WinLinesFromView(this.view, player.betPerLine);

    this.virtualReels = {
        above: RandomLineFromReels(baseReels),
        below: RandomLineFromReels(baseReels),
    };

    // 스캣터당첨뷰;
    if (isFreeSpinWin(this.view)) {
        this.freeSpinIndex = 1;

        this.scatterPositions = ScatterPositions(this.view);
        this.freeSpinBeforeMoney = this.winMoney;
        this.freeSpinWinMoney = this.winMoney;
        this.currentGame = "FREE";
    }

    // 보너스당첨뷰
    if (isFullStackWin(this.view)) {
        this.superBonusCacheIndex = 1;
        this.moneyBonusWin = this.winMoney;
        this.currentGame = "BONUS";
    }
};

SlotMachine.prototype.FreeSpin = function (player) {
    this.view = this.freeSpinCacheList[this.freeSpinIndex];

    this.winMoney = WinFromView(this.view, player.betPerLine);
    this.winLines = WinLinesFromView(this.view, player.betPerLine);

    this.virtualReels = {
        above: RandomLineFromReels(freeReels),
        below: RandomLineFromReels(freeReels),
    };

    this.freeSpinWinMoney += this.winMoney;

    this.freeSpinIndex++;
    if (this.freeSpinIndex > this.freeSpinLength) {
        this.currentGame = "BASE";
    }
};

SlotMachine.prototype.BonusSpin = function (player) {
    this.gameSort = this.currentGame;

    var superBonusCache = this.superBonusCacheList[this.superBonusCacheIndex];
    this.view = superBonusCache.view;
    this.superBonusCount = superBonusCache.count;
    this.superBonusMax = superBonusCache.max;

    this.superBonusCacheIndex++;
    this.winMoney = 0;

    if (this.superBonusCacheIndex >= this.superBonusCacheList.length) {
        // 잭팟
        var superCount = NumberOfSupers(this.view);
        if (superCount == 14) {
            this.winMoney = player.betPerLine * this.lineCount * 375;
        } else if (superCount == 15) {
            this.winMoney = player.betPerLine * this.lineCount * 500;
        } else {
            this.winMoney = WinFromView(this.view, player.betPerLine);
            this.winLines = WinLinesFromView(this.view, player.betPerLine);
        }

        this.moneyBonusWin = this.winMoney;
        this.currentGame = "BASE";
    }
};

SlotMachine.prototype.SpinForBaseGen = function (bpl, totalBet, baseWin) {
    var tmpView, tmpWin;

    if (baseWin > 0) {
        tmpView = RandomWinView(baseReels, bpl, baseWin);
    } else {
        tmpView = RandomZeroView(baseReels, bpl);
    }
    tmpWin = WinFromView(tmpView, bpl);

    var pattern = {
        view: tmpView,
        win: tmpWin,
        type: "BASE",
        bpl: bpl,
    };
    return pattern;
};

SlotMachine.prototype.SpinForJackpot = function (bpl, totalBet, jpWin, isCall = false, jpType) {
    var newJpType = jpType;
    if (jpType === "RANDOM") {
        newJpType = this.jackpotType[Util.random(0, this.jackpotType.length)];
    }

    switch (newJpType) {
        case "FREE":
            return this.SpinForFreeGen(bpl, totalBet, jpWin, isCall);
        case "BONUS":
            return this.SpinForBonusGen(bpl, totalBet, jpWin, isCall);
        default:
            return;
    }
};

SlotMachine.prototype.SpinForFreeGen = function (bpl, totalBet, fsWin, isCall = false) {
    var scatterView = RandomScatterView(baseReels);
    var scatterWinMoney = ScatterWinFromView(scatterView, totalBet) + WinFromView(scatterView, bpl);
    var freeSpinData = {
        length: this.freeSpinCount,
        viewList: [],
    };

    // 프리스핀 캐쉬 생성
    var cache = RandomFreeViewCache(freeReels, bpl, fsWin, freeSpinData.length);

    freeSpinData.viewList.push(scatterView);
    freeSpinData.viewList = freeSpinData.viewList.concat(cache.viewList);

    return {
        win: cache.win + scatterWinMoney,
        bpl: bpl,
        view: freeSpinData,
        type: "FREE",
        isCall: isCall ? 1 : 0,
    };
};

SlotMachine.prototype.SpinForBonusGen = function (bpl, totalBet, bsWin, isCall = false) {
    var bonusView = RandomBonusView(baseReels, bpl);
    var bonusViewWinMoney = WinFromView(bonusView, bpl);

    var superBonusCache = RandomBonusViewCache(baseReels, bpl, bsWin - bonusViewWinMoney, totalBet, bonusView);
    superBonusCache.cache[0].view = bonusView;

    var pattern = {
        view: superBonusCache.cache,
        bpl: bpl,
        win: superBonusCache.win,
        type: "BONUS",
        isCall: isCall ? 1 : 0,
    };

    return pattern;
};

var RandomWinView = function (reels, bpl, maxWin) {
    var tmpView, tmpWin;
    var bottomLimit = 0;
    var calcCount = 0;

    while (true) {
        tmpView = RandomView(reels);
        tmpWin = WinFromView(tmpView, bpl);
        if (tmpWin > bottomLimit && tmpWin <= maxWin) {
            break;
        }
        calcCount++;
        if (calcCount > 100) {
            return RandomZeroView(reels, bpl);
        }
    }
    return tmpView;
};

var RandomZeroView = function (reels, bpl) {
    var tmpView, tmpWin;

    while (true) {
        tmpView = RandomView(reels);

        tmpWin = WinFromView(tmpView, bpl);
        if (tmpWin == 0) {
            break;
        }
    }
    return tmpView;
};

var RandomView = function (reels) {
    var view = [];

    while (true) {
        for (var i = 0; i < slotWidth; i++) {
            var len = reels[i].length;
            var randomIndex = Util.random(0, len);
            for (var j = 0; j < slotHeight; j++) {
                var viewPos = i + j * slotWidth;
                var reelPos = (randomIndex + j) % len;
                view[viewPos] = reels[i][reelPos];
            }
        }

        if (!isFreeSpinWin(view) && !isFullStackWin(view)) {
            break;
        }
    }
    return view;
};

var RandomScatterView = function (reels) {
    var view = [];

    while (true) {
        for (var i = 0; i < slotWidth; i++) {
            var len = reels[i].length;
            var randIdx = Util.random(0, len);
            for (var j = 0; j < slotHeight; j++) {
                var pos = i + j * slotWidth;
                var reelPos = (randIdx + j) % len;
                view[pos] = reels[i][reelPos];
                if (isScatter(view[pos])) {
                    view[pos] = Util.random(3, 12);
                }
            }
        }

        var reelNoArr = [1, 2, 3];

        for (var i = 0; i < reelNoArr.length; i++) {
            var height = Util.random(0, slotHeight);
            var pos = height * slotWidth + reelNoArr[i];
            view[pos] = scatter;
        }

        if (!isFullStackWin(view)) {
            break;
        }
    }
    return view;
};

var RandomFreeViewCache = function (reels, bpl, fsWin, fsLen) {
    var minMoney = fsWin * 0.8;
    var maxMoney = fsWin;

    minMoney = Util.max(minMoney, 0);
    maxMoney = Util.max(maxMoney, 0);

    var lowerLimit = -1,
        upperLimit = 100000000000000;
    var lowerView = null,
        upperView = null;

    for (var patternIndex = 0; patternIndex < 200; patternIndex++) {
        var freeSpinIndex = 1;
        var freeSpinData = {};
        freeSpinData.viewList = [];
        var freeSpinWinMoney = 0;
        var freeSpinLength = fsLen;

        while (true) {
            var fsview, fsWin;
            while (true) {
                fsview = RandomView(reels);
                fsWin = WinFromView(fsview, bpl);

                if (Util.probability(50) || fsWin == 0) {
                    break;
                }
            }

            freeSpinData.viewList.push(fsview);

            freeSpinWinMoney += fsWin;

            freeSpinIndex++;

            if (freeSpinIndex > freeSpinLength) {
                freeSpinData.win = freeSpinWinMoney;
                break;
            }
        }

        if (freeSpinData.win >= minMoney && freeSpinData.win <= maxMoney) {
            return freeSpinData;
        }

        if (freeSpinData.win > lowerLimit && freeSpinData.win < minMoney) {
            lowerLimit = freeSpinData.win;
            lowerView = freeSpinData;
        }
        if (freeSpinData.win > maxMoney && freeSpinData.win < upperLimit) {
            upperLimit = freeSpinData.win;
            upperView = freeSpinData;
        }
    }

    return lowerView ? lowerView : upperView;
};

var RandomBonusView = function (reels, bpl) {
    var view = [];

    while (true) {
        for (var i = 0; i < slotWidth; i++) {
            var len = reels[i].length;
            var randomIndex = Util.random(0, len);
            for (var j = 0; j < slotHeight; j++) {
                var viewPos = i + j * slotWidth;
                var reelPos = (randomIndex + j) % len;
                view[viewPos] = reels[i][reelPos];
            }
        }

        if (isFullStackWin(view) && WinFromView(view, bpl) == 0 && !isFreeSpinWin(view)) {
            break;
        }
    }
    return view;
};

var RandomBonusViewCache = function (reels, bpl, bsWin, totalBet, bonusView) {
    // 첫슈퍼스핀뷰 캐쉬 생성
    var firstView = GetSuperSpinFirstView(bonusView);
    var firstCache = {
        view: firstView,
        count: 0,
        max: 3,
    };

    var minMoney = bsWin * 0.8;
    var maxMoney = bsWin;

    minMoney = Util.max(minMoney, 0);
    maxMoney = Util.max(maxMoney, 0);

    var lowerLimit = -1,
        upperLimit = 100000000000000;
    var lowerView = null,
        upperView = null;

    for (var patternIndex = 0; patternIndex < 200; patternIndex++) {
        var bonusSpinCache = {};
        var bonusSpinCacheList = [firstCache];
        var bonusSpinIndex = 0;
        var bonusSpinLength = 3;
        var bonusSpinWinMoney = 0;

        while (true) {
            bonusSpinIndex++;

            var lastCache = bonusSpinCacheList[bonusSpinCacheList.length - 1];
            var newView = Util.clone(lastCache.view);

            var randomView = RandomBonusCacheView(reels);
            var randomSuperView = GetSuperSpinView(randomView);

            var isAdded = false;
            for (var i = 0; i < newView.length; i++) {
                if (newView[i] != superSymbol && randomSuperView[i] == superSymbol) {
                    isAdded = true;
                    newView[i] = superSymbol;
                }
            }

            // 슈퍼심벌이 추가되면
            if (isAdded) {
                bonusSpinLength++;
            }

            var newCache = {
                view: [...newView],
                count: bonusSpinIndex,
                max: bonusSpinLength,
            };

            bonusSpinCacheList.push(newCache);

            var superCount = NumberOfSupers(newView);

            if (bonusSpinIndex == bonusSpinLength || superCount >= 14) {
                // 잭팟
                if (superCount == 14) {
                    bonusSpinWinMoney = totalBet * 375;
                } else if (superCount == 15) {
                    bonusSpinWinMoney = totalBet * 500;
                } else {
                    bonusSpinWinMoney = WinFromView(newView, bpl);
                }
                break;
            }
        }

        bonusSpinCache = {
            cache: [...bonusSpinCacheList],
            win: bonusSpinWinMoney,
        };

        // 랜덤보너스스핀 생성 완료
        if (bonusSpinCache.win >= minMoney && bonusSpinCache.win <= maxMoney) {
            return bonusSpinCache;
        }

        if (bonusSpinCache.win > lowerLimit && bonusSpinCache.win < minMoney) {
            lowerLimit = bonusSpinCache.win;
            lowerView = bonusSpinCache;
        }
        if (bonusSpinCache.win > maxMoney && bonusSpinCache.win < upperLimit) {
            upperLimit = bonusSpinCache.win;
            upperView = bonusSpinCache;
        }
    }

    return lowerView ? lowerView : upperView;
};

var RandomBonusCacheView = function (reels) {
    var view = [];

    for (var i = 0; i < slotWidth; i++) {
        var len = reels[i].length;
        var randomIndex = Util.random(0, len);
        for (var j = 0; j < slotHeight; j++) {
            var viewPos = i + j * slotWidth;
            var reelPos = (randomIndex + j) % len;
            view[viewPos] = reels[i][reelPos];
        }
    }

    return view;
};

var RandomLineFromReels = function (reels) {
    var result = [];

    for (var i = 0; i < slotWidth; i++) {
        var index = Util.random(0, reels[i].length);
        result[i] = reels[i][index];
    }

    return result;
};

var GetSuperSpinView = function (view) {
    var superView = Util.clone(view);

    for (var i = 0; i < superView.length; i++) {
        if (superView[i] != superSymbol) {
            superView[i] = emptySymbol;
        }
    }

    return superView;
};

var GetSuperSpinFirstView = function (view) {
    var superView = Util.clone(view);

    for (var i = 0; i < superView.length; i++) {
        if (superView[i] != superSymbol) {
            superView[i] = emptySymbol;
        }
    }

    var stackReels = [];

    for (var i = 0; i < slotWidth; i++) {
        var isFullStack = true;
        for (var j = 0; j < slotHeight; j++) {
            var pos = i + j * slotWidth;
            if (view[pos] != superSymbol) {
                isFullStack = false;
                break;
            }
        }
        if (isFullStack) {
            stackReels.push(i);
        }
    }

    for (var j = 0; j < superView.length; j++) {
        if (superView[j] == superSymbol) {
            if (stackReels.indexOf(j % slotWidth) == -1) {
                superView[j] = emptySymbol;
            }
        }
    }

    return superView;
};

var WinFromView = function (view, bpl) {
    var winMoney = 0;

    for (var lineId = 0; lineId < payLines.length; lineId++) {
        var line = payLines[lineId];
        var lineSymbols = Util.symbolsFromLine(view, line);
        var linePay = WinFromLine(lineSymbols, bpl);
        winMoney += linePay;
    }

    return winMoney;
};

var WinFromLine = function (lineSymbols, bpl) {
    // 매칭 심벌 갯수
    var matchCount = 0;

    // 기준심벌을 처음에 와일드로 잡기
    var symbol = wild;

    //기준심볼 정의
    for (var i = 0; i < lineSymbols.length; i++) {
        if (isWild(lineSymbols[i]))
            //첫 심볼이 와일드면 다음 심볼얻기
            continue;

        symbol = lineSymbols[i];
        break;
    }

    // 와일드패인경우 기준심볼로 대체하기
    for (var i = 0; i < lineSymbols.length; i++) {
        if (isWild(lineSymbols[i])) {
            hasWild = true;
            lineSymbols[i] = symbol;
        }
    }

    //심볼의 련이은 갯수얻기
    for (var i = 0; i < lineSymbols.length; i++) {
        if (lineSymbols[i] != symbol) break;
        matchCount++;
    }

    //매칭되는 심볼까지가고 나머지는 -1로, 이 lineSymbols를 밑에서 또 쓴다.
    for (var i = matchCount; i < lineSymbols.length; i++) {
        lineSymbols[i] = -1;
    }

    var winPay = payTable[matchCount][symbol] * bpl;
    return winPay;
};

var WinLinesFromView = function (view, bpl) {
    var winLines = [];

    for (var lineId = 0; lineId < payLines.length; lineId++) {
        var line = payLines[lineId];
        var lineSymbols = Util.symbolsFromLine(view, line);
        var money = WinFromLine(lineSymbols, bpl);
        if (money > 0) {
            winLines.push(
                `${lineId}~${money}~${line
                    .filter(function (item, index, arr) {
                        return lineSymbols[index] != -1;
                    })
                    .join("~")}`
            );
        }
    }
    return winLines;
};

var isWild = function (symbol) {
    return symbol == wild || symbol == wildInFree;
};

var isScatter = function (symbol) {
    return symbol == scatter;
};

var isSuper = function (symbol) {
    return symbol == superSymbol;
};

var isFreeSpinWin = function (view) {
    return NumberOfScatters(view) >= 3;
};

var isFullStackWin = function (view) {
    var fullStackCount = 0;
    for (var i = 0; i < slotWidth; i++) {
        var isFullStack = true;
        for (var j = 0; j < slotHeight; j++) {
            var pos = i + j * slotWidth;
            if (view[pos] != superSymbol) {
                isFullStack = false;
                break;
            }
        }
        if (isFullStack) {
            fullStackCount++;
        }
    }
    return fullStackCount >= 2;
};

var NumberOfScatters = function (view) {
    var result = 0;
    for (var i = 0; i < view.length; i++) {
        if (isScatter(view[i])) {
            result++;
        }
    }
    return result;
};

var NumberOfSupers = function (view) {
    var result = 0;
    for (var i = 0; i < view.length; i++) {
        if (isSuper(view[i])) {
            result++;
        }
    }
    return result;
};

var ScatterWinFromView = function (view, totalBet) {
    if (isFreeSpinWin(view)) {
        return totalBet * 2;
    }
    return 0;
};

var ScatterPositions = function (view) {
    var result = [];
    for (var i = 0; i < view.length; i++) {
        if (isScatter(view[i])) {
            result.push(i);
        }
    }
    return result;
};

module.exports = SlotMachine;