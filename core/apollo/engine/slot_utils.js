exports.max = function (num1, num2) {
    return num1 > num2 ? num1 : num2;
}

exports.min = function (num1, num2) {
    return num1 < num2 ? num1 : num2;
}

exports.maxInArr = function (arr) {
    var max = arr[0];
    var maxIndex = 0;
    for (var i = 1; i < arr.length; i++) {
        if (max < arr[i]) {
            max = arr[i];
            maxIndex = i;
        }
    }
    var res = {
        value: max,
        index: maxIndex
    }
    return res;
}

exports.minInArr = function (arr) {
    var min = arr[0];
    var minIndex = 0;
    for (var i = 1; i < arr.length; i++) {
        if (min > arr[i]) {
            min = arr[i];
            minIndex = i;
        }
    }
    var res = {
        value: min,
        index: minIndex
    }
    return res;
}

// 무작위수(min포함, max포함안함)
exports.random = function (min, max) {
    var result = min + Math.floor(Math.random() * (max - min));
    return result;
}



exports.randomMoney = function (totalBet, max) {
    var div = Math.floor(max / totalBet);
    var mul = this.random(0, div);
    return totalBet * mul;
}

// 확율
exports.probability = function (prob) {
    var n = Math.floor(Math.random() * 100); //0 - 99 난수
    if (n < prob)
        return true;
    else
        return false;
}

// 배열 무작위
exports.shuffle = function (arr) {
    if (arr.length <= 0) {
        return arr;
    }

    let a = 0;
    let b = 0;
    let tmp;
    for (var i = 0; i < arr.length; i++) {
        a = this.random(0, arr.length);
        b = this.random(0, arr.length);

        tmp = arr[a];
        arr[a] = arr[b];
        arr[b] = tmp;
    }

    return arr;
};

// 뷰를 문자열로
exports.view2String = function (view) {
    return view.join();
}

// 라인에 따르는 심벌배열얻기
exports.symbolsFromLine = function (view, payLine) {
    var result = [];

    for (let i = 0; i < payLine.length; i++) {
        var index = payLine[i];
        result[i] = view[index];
    }

    return result;
}

// 심벌개수얻기
exports.symbolCountFromView = function (view, symbol) {
    var result = 0;
    for (var i = 0; i < view.length; i++) {
        if (view[i] == symbol)
            result++;
    }
    return result;
}

// 배열 클론
exports.clone = function (arr) {
    var cloned = [];
    arr.forEach(
        function (item) {
            cloned.push(item);
        }
    );

    return cloned;
};

// 배열에서 특정 첨수의 요소 삭제
exports.remove = function (arr, removeIndex) {
    var removed = [];
    for (var i = 0; i < arr.length; i++) {
        if (i == removeIndex)
            continue;
        removed.push(arr[i]);
    }
    return removed;
};


// 배열에서 특정 첨수의 요소 카운트
exports.count = function (arr, element) {
    var count = 0;
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] == element) {
            count++;
        }
    }
    return count;
};

/**
 * @author Rich
 * randomPositionArray - 무작위 얻기
 * @param {*} w - width
 * @param {*} h - height
 * @param {*} c - count
 */
exports.randomPositionArray = function (w, h, c) {
    let result = [];
    for (let i = 0; i < w * h; i++) {
        result.push(i);
    }
    result = this.shuffle(result);
    return result.slice(0, c);
}

/**
 * @author JackSon
 * sameArray - 같은 값들로 채운 배열
 */
exports.sameArray = function (len, value) {
    let result = [];
    for (let i = 0; i < len; i++) {
        result.push(value);
    }
    return result;
}
/**
 * @author JackSon
 * positionsFromView - 뷰에서 해당 조건을 만족하는 심벌 위치들 얻기
 */
exports.positionsFromView = function (view, func = Function(item)) {
    var res = [];

    for (var i = 0; i < view.length; ++i) {
        if (func(view[i])) {
            res.push(i);
        }
    }

    return res;
}


exports.getMaskView = function (view, max, func = Function(item)) {
    var res = [...view];

    for (var i = 0; i < view.length; ++i) {
        if (func(view[i])) {
            res[i] = this.random(3, max);
        }
    }

    return res;
}

exports.Result4Client = function (obj) {
    var str = "";
    for (let index in obj) {
        str += index + "=" + obj[index] + "&";
    }
    return str;
}
//=================================================================================
// 상수


exports.MIN_FACTOR = 0.8;
exports.MAX_FACTOR = 1.2;