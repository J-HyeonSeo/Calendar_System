
<div class="statistics-area" id="day-statistics">
    <div class="search-form-wrap">
        <h2 class="statistics-title">일별 할당된 일정 통계</h2>
        <form class="search-form">
            <input class="start-date" type="date"/> -
            <input class="end-date" type="date"/>
            <img class="search-btn" onclick="dataCallAndSettingChart('day')" alt="search" src="/public/images/search.png"/>
        </form>
    </div>
    <div id="day-chart" class="chart-content"></div>
</div>

<div class="statistics-area" id="month-statistics">
    <div class="search-form-wrap">
        <h2 class="statistics-title">월별 할당된 일정 통계</h2>
        <form class="search-form">
            <input class="start-date" type="date"/> -
            <input class="end-date" type="date"/>
            <img class="search-btn" onclick="dataCallAndSettingChart('month')" alt="search" src="/public/images/search.png"/>
        </form>
    </div>
    <div id="month-chart" class="chart-content"></div>
</div>

<div class="statistics-area" id="participant-statistics">
    <div class="search-form-wrap">
        <h2 class="statistics-title">참가자별 할당된 일정 통계</h2>
        <form class="search-form">
            <input class="start-date" type="date"/> -
            <input class="end-date" type="date"/>
            <img class="search-btn" onclick="dataCallAndSettingChart('participant')" alt="search" src="/public/images/search.png"/>
        </form>
    </div>
    <div id="participant-chart" class="chart-content"></div>
</div>

<div class="statistics-area" id="place-statistics">
    <div class="search-form-wrap">
        <h2 class="statistics-title">장소별 할당된 일정 통계</h2>
        <form class="search-form">
            <input class="start-date" type="date"/> -
            <input class="end-date" type="date"/>
            <img class="search-btn" onclick="dataCallAndSettingChart('place')" alt="search" src="/public/images/search.png"/>
        </form>
    </div>
    <div id="place-chart" class="chart-content"></div>
</div>

<div class="statistics-area" id="type-statistics">
    <div class="search-form-wrap">
        <h2 class="statistics-title">종류별 할당된 일정 통계</h2>
        <form class="search-form">
            <input class="start-date" type="date"/> -
            <input class="end-date" type="date"/>
            <img class="search-btn" onclick="dataCallAndSettingChart('type')" alt="search" src="/public/images/search.png"/>
        </form>
    </div>
    <div id="type-chart" class="chart-content"></div>
</div>


<!-- chart-data 조회 및 할당 관련 스크립트 -->
<script>
    // 일/월별 차트 표시
    // ECharts 인스턴스 생성
    const dayChart = echarts.init(document.getElementById('day-chart'));
    const monthChart = echarts.init(document.getElementById('month-chart'));
    const participantChart = echarts.init(document.getElementById('participant-chart'));
    const placeChart = echarts.init(document.getElementById('place-chart'));
    const typeChart = echarts.init(document.getElementById('type-chart'));

    // 오늘 날을 기준으로, 월초, 월말 구하기.
    const today = new Date();
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    const formatDate = (date) => {
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    };

    $('.start-date').val(formatDate(startOfMonth));
    $('.end-date').val(formatDate(endOfMonth));


    // 최초 그래프 할당하기.
    dataCallAndSettingChart('day');
    dataCallAndSettingChart('month');
    dataCallAndSettingChart('participant');
    dataCallAndSettingChart('place');
    dataCallAndSettingChart('type');

    // 차트 유형과 날짜 범위를 입력받아서, 셋팅.
    function dataCallAndSettingChart(chartType) {

        const startDate = $(`#${chartType}-statistics .start-date`).val();
        const endDate = $(`#${chartType}-statistics .end-date`).val();

        // 시작 시간은 반드시 입력
        if (startDate === undefined ||
            startDate === null ||
            startDate === ''
        ) {
            alert("시작 일자를 지정해주세요.");
            return false;
        }

        // 종료 시간은 반드시 입력
        if (endDate === undefined ||
            endDate === null ||
            endDate === ''
        ) {
            alert("종료 일자를 지정해주세요.");
            return false;
        }

        // 시작 시간 < 종료 시간
        if (new Date(startDate) > new Date(endDate)) {
            alert("시작 시간이 종료 시간와 동일하거나 넘어설 수 없습니다.");
            return false;
        }

        $.ajax({
            url: `/statistics/${chartType}?startDate=${startDate}&endDate=${endDate}`,
            type: 'GET', // HTTP 메서드
            success: function (response) {
                const chartList = response.chartList;
                switch (chartType) {
                    case 'day':
                        settingChart(dayChart, '일별 통계', chartList, '#600c6a');
                        break;
                    case 'month':
                        settingChart(monthChart, '월별 통계', chartList, '#18937e');
                        break;
                    case 'participant':
                        settingChart(participantChart, '참가자별 통계', chartList, '#11983e');
                        break;
                    case 'place':
                        settingChart(placeChart, '장소별 통계', chartList, '#d67615');
                        break;
                    case 'type':
                        settingChart(typeChart, '종류별 통계', chartList);
                        break;
                }
            },
            error: function () {
                console.error(chartType + "에 대한 차트정보를 조회하지 못하였음.");
            }
        });
    }









    // const dayData = [
    //     { xData: '2024-04-01', yData: 8.5 },
    //     { xData: '2024-04-02', yData: 9.2 },
    //     { xData: '2024-04-03', yData: 27.8 },
    //     { xData: '2024-04-04', yData: 100.0 },
    //     { xData: '2024-04-05', yData: 6.5 }
    // ];
    // settingChart(dayChart, '일별 통계', dayData, '#600c6a');
    //
    // const monthData = [
    //     { xData: '2024-04', yData: 8.5 },
    //     { xData: '2024-05', yData: 9.2 },
    //     { xData: '2024-06', yData: 27.8 },
    //     { xData: '2024-07', yData: 140.0 },
    //     { xData: '2024-08', yData: 6.5 }
    // ];
    // settingChart(monthChart, '월별 통계', monthData, '#18937e')
    //
    // const participantData = [
    //     { xData: 'Jerry', yData: 8.5 },
    //     { xData: 'Jason', yData: 26.2 },
    //     { xData: 'Patrick', yData: 27.8 },
    //     { xData: 'James', yData: 100.0 },
    //     { xData: 'Sarah', yData: 6.5 }
    // ];
    // settingChart(participantChart, '참가자별 통계', participantData, '#11983e');
    //
    // const placeData = [
    //     { xData: '롯데월드', yData: 66.5 },
    //     { xData: '닭갈비집', yData: 26.2 },
    //     { xData: '레고랜드', yData: 27.8 },
    //     { xData: '에버랜드', yData: 100.0 },
    //     { xData: '롯데시그니엘호텔', yData: 6.5 }
    // ];
    // settingChart(placeChart, '장소별 통계', placeData, '#d67615');
    //
    // const typeData = [
    //     { xData: '일반', yData: 67.5 },
    //     { xData: '교육', yData: 26.2 },
    //     { xData: '세미나', yData: 27.8 },
    //     { xData: '회식', yData: 100.0 },
    // ];
    // settingChart(typeChart, '종류별 통계', typeData);






    // 차트를 새롭게 셋팅하는 함수
    function settingChart(chart, title, chartData, color='#000000') {

        // x축 데이터
        const xAxisData = chartData.map(item => item.xData);
        // y축 데이터
        const yAxisData = chartData.map(item => Number(item.yData));

        // y축 최소/최대 자동 계산
        const minTime = Math.floor(Math.min(...yAxisData)) - 1; // 최소값보다 1시간 낮게
        const maxTime = Math.ceil(Math.max(...yAxisData)) + 1;  // 최대값보다 1시간 높게

        const option = {
            title: {
                text: title,
                left: 'center'
            },
            tooltip: {
                trigger: 'axis',
                formatter: function (params) {
                    var value = params[0].value;
                    return `${params[0].axisValue} <br/> ${value.toFixed(1)} 시간`;
                }
            },
            xAxis: {
                type: 'category',
                data: xAxisData,
                axisLabel: {
                    rotate: 35 // 날짜가 겹치지 않도록 회전
                }
            },
            yAxis: {
                type: 'value',
                min: minTime,
                max: maxTime,
                axisLabel: {
                    formatter: function (value) {
                        return `${value.toFixed(1)} 시간`;
                    }
                }
            },
            series: [
                {
                    name: '시간',
                    type: 'line',
                    data: yAxisData,
                    itemStyle: {
                      color: color
                    },
                    lineStyle: {
                      color: color
                    },
                    smooth: false,
                    label: {
                        show: true,
                        formatter: function (param) {
                            return `${param.value.toFixed(1)} 시간`;
                        }
                    }
                }
            ]
        };

        chart.setOption(option);
    }


</script>