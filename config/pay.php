<?php

return [
    'alipay' =>[
        'app_id' => '2016092400581942',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0KVesIzTN5OdMl7FF1W1T57cf8YaOCx16YbvCg2NQdOEYRlPnH4F+7CId/3vHoH4KG7Q9K+32lXKkcjrnqEct7+n6JDj63eBQOKWj9E+MKu7C6PrZLPR5CkwCGGtAmqoCWMr43Zvz/4hX1rJl6kyP9+RDViOav1qpZJr2yGKRb4Q/VdSL/Qd4YxmHaAlYKxyyvw+pXhTgaol8kCBmaRx1q49Ewq3GJfpv1gyCIeiBL8HaTyePzNBT+/zBqohJOte3arhtV82QkdjaqjDhNqMlHZ8mt9fypLFK8/mcTOAtys+w46NrXjLhPX1wCGAHKJk4THg81zDW42Mo31FZJN99wIDAQAB',
        'private_key' => 'MIIEogIBAAKCAQEAnQ9yDv+ZUq6E3B8OW2LnX7w8vG1/MdCU6nS5lwqaG1Zmo5j96A5GiL4lK3p3Jr7wjanS8NjonXprMx0Gzq7sTCPwD2geyizXLnY50Y4XJUS86jM0R6obZ7Gjc1LQAAHmwIUVsyjkQbnPlkF2ajQNZFudD+QGOAYs0QEoQWwplQnQ0kw9Fn6lj67tDWfc50m2vBQwZypbzcxILH6BZmtWesE1dNY/0u563w7e8E/0Xaee2zEXAx9SY4d9pc/XCsFNgz0qFDrAnaaM0745zFAOF3qqrMXcGFh7477a6UH+Pr0SB9i09ahyRGlYqe/6Gg6TrC+ktIId4vP8q2WN/LPMoQIDAQABAoIBAGzu/I4Yi7BEKRQ/WgbaZ14IQhvy2iij4dVn1h1DljVlzNSlVV3xIMzRDRjF3QR7pH6tDghMebJX6ShPdpw9yNjaiDM3ZTuWtDyedWW55qXLsjfCAf2+pzlZhOOEyY1nxDM9WxqjPwWzKZ6uksIAjlVQEXSnFAUhi03/Pnt52LFsQkVChj6A1rKd3q/+6wrrTT5N4OyHC0c7NQP18NhnGzq9WBS9GMqkWKHEUgVI07EwqEr6A5y04iD66zdIHkKJ1Wu2JOZZAlQbojRuEx+ZXoT/PtOFXO8e6lsTEY1m9WAXbfBstE1wf5k9VnQ+MmxdZrDZXH+tGKqLD22W+DZeza0CgYEAyrKHJF6DEkQ0T0hyvB+qnGWoWqQVXB99VKQEmq/89vwl3ycMnQiIRrxzIdQgyhirPyRnFfWxFPOyjWKuXtZGVKJkpGNJBbE4qqrguWuIhriZ2FZLDRspFT2nVBTh1Iq5Q2visTgfRF2wio6t4KxeXB+PhWpc+aq/4j+UemKmGJMCgYEAxlyocxx5F+bq2/VCYZZkB0xW4JQmtln3oduTZHpFX+JPvZT8VOhvAT0Z7eKMO61+779WLgwxxsACA/efE2O3BpyOJ5QhujgJzVIyTd3yU0R3OUNNSHHc7JIismmdFyx+mDMofVgwrcDkehdswH3Evr+Tzw/4KbCwcgcr8BW2ynsCgYAAg29AMlUWqMGRKdfQjiv6dW91u1CqiRHiMwAn/CYh4gWeiRTJGQpvU27fORBUMrfMUyVHsvWd3fgnz4Yti1FmSXsDx5SZxRclp4UmMxWHcdRB7gYGpHj4Ks3PsrXXufo6J33NgRzjKXGXCEF8fjNG/HV6mwhdQYd25T5VCQburQKBgBhD43NZa1LKM7a+lOU9MXVXGFWWqfmqnclWA/zJnEzzcU++tvNoD5Q2NkLMcN0oBxwq6s7tPPWULWrw0qvbidssMZOT4mBWVJSiVncCDI+1E2SU4BJZo23pHod2tfnMvpu9vlk+/Y1zyD9LOEl/2R6AbwNob7Ih5CO+ztgqv9abAoGAZdIgLa6tKnwDrq5fc3N27xsKtxwiydf/wmBZPcJ2kdFMCUqiXq+dK95DRgGwxR8e6Si4jb70KUVjwGtqzMNG8Tq4w7DtmH9rE1PjESdDtOuapiCy33LLPQBLhNyk40JsSncOfSVraGt/WITvHk6VyBEJKsUt8cGTLILvFGeOAME=',
        'log' => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key'    => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'    => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];