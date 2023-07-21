 <!DOCTYPE html>
<html>
<head>
    <title>IP查询</title>
   <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            max-width: 95%;
            padding: 5px;
            margin-bottom: 10px;
        }

        button {
            padding: 5px 40px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #currentIP {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        #result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        #errorText {
            color: red;
            display: none;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>IP查询</h1>
    <form>
       <!--  <label for="ip">请输入IP地址：</label>-->
        <input type="text" id="ip" required>
        <button type="button" onclick="queryIP()">查询</button>
    </form>

    <div id="currentIP">
        <p>当前IPV4地址： <span id="ipv4Address"></span></p>
        <p>当前IPV6地址： <span id="ipv6Address"></span></p>
        <p>网络运营商： <span id="isp"></span></p>
        <p>地区城市： <span id="city"></span></p>
    </div>

    <div id="result">
        <!-- 查询结果将显示在这里 -->
    </div>

    <div id="errorText"></div>

    <script>
        // 获取用户的当前IP地址和详细信息
        function getCurrentIP() {
            fetch("https://api.ipify.org?format=json")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("ipv4Address").innerText = data.ip;
                    getCurrentIPDetails(data.ip);
                })
                .catch(error => console.error("获取IP地址失败：" + error));
            
            fetch("https://api6.ipify.org?format=json")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("ipv6Address").innerText = data.ip;
                })
                .catch(error => console.error("获取IPv6地址失败：" + error));
        }

        // 获取当前IP的详细信息
        function getCurrentIPDetails(ip) {
            var apiUrl = "api.php?ip=" + ip;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("isp").innerText = data.company.name;
                    document.getElementById("city").innerText = data.city;
                })
                .catch(error => console.error("获取IP详细信息失败：" + error));
        }

        // 查询指定IP地址的信息
        function queryIP() {
            var ipInput = document.getElementById("ip");
            var errorText = document.getElementById("errorText");

            // 隐藏错误提示
            errorText.style.display = "none";

            // 检查是否输入了IP地址
            if (ipInput.value.trim() === "") {
                // 显示错误提示：未输入IP地址
                errorText.innerText = "请输入IP地址";
                errorText.style.display = "block";
                return;
            }

            // 检查是否输入了有效的IP地址
            if (!validateIP(ipInput.value)) {
                // 显示错误提示：IP地址格式不正确
                errorText.innerText = "请输入有效的IP地址";
                errorText.style.display = "block";
                return;
            }

            // 执行查询
            var apiUrl = "api.php?ip=" + ipInput.value;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        // 显示错误提示：查询失败
                        errorText.innerText = data.error;
                        errorText.style.display = "block";
                        return;
                    }

                    var resultDiv = document.getElementById("result");
                    var ipType = data.privacy.hosting ? "数据中心IP" : "家庭宽带IP";

                    resultDiv.innerHTML = "<p>IP地址：" + data.ip + "</p>" +
                                            "<p>类型：" + ipType + "</p>" +
                                            "<p>国家：" + data.country + "</p>" +
                                            "<p>地区：" + data.region + "</p>" +
                                            "<p>城市：" + data.city + "</p>" +
                                            "<p>注册人信息：" + data.company.name + "</p>" +
                                            "<p>ASN：" + data.asn.asn + " (" + data.asn.name + ")</p>" +
                                            "<p>Abuse地址：" + data.abuse.address + "</p>" +
                                            "<p>Abuse邮件：" + data.abuse.email + "</p>" +
                                            "<p>Abuse电话：" + data.abuse.phone + "</p>" +
                                            "<p>解析到此IP上的域名：</p>" +
                                            "<ul>" + getDomainsList(data.domains.domains) + "</ul>";
                })
                .catch(error => {
                    // 显示错误提示：查询失败
                    errorText.innerText = "查询失败，请稍后再试";
                    errorText.style.display = "block";
                    console.error("查询失败：" + error);
                });
        }

        // 校验IP地址是否有效
        function validateIP(ip) {
            var ipPattern = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
            return ipPattern.test(ip);
        }

        // 解析域名列表
        function getDomainsList(domains) {
            if (domains && domains.length > 0) {
                return domains.map(domain => "<li>" + domain + "</li>").join("");
            } else {
                return "<li>没有解析到此IP的域名</li>";
            }
        }

        // 页面加载后获取当前IP地址和详细信息
        document.addEventListener("DOMContentLoaded", function () {
            getCurrentIP();
            document.getElementById("ip").addEventListener("input", function () {
                // 隐藏错误提示
                document.getElementById("errorText").style.display = "none";
            });
        });
    </script>
</body>
</html>

