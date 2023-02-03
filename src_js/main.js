import axios, {isCancel, AxiosError} from 'axios';


import "./main.scss"

(function (  ){

    const file = document.querySelector('input#fileUpload')
    file.addEventListener('change', readFileToUpload)
    async function readFileToUpload() {
        const file = this.files[0]
        console.log(file)

        const reader = new FileReader()

        reader.onload = function (){
            console.log(this.result)
        }

        reader.readAsArrayBuffer(file)
        //reader.readAsText(file)

    }


    // axios.request({
    //     method: "post",
    //     url: "/aaa",
    //     data: myData,
    //     onUploadProgress: (p) => {
    //         console.log(p);
    //         //this.setState({
    //         //fileprogress: p.loaded / p.total
    //         //})
    //     }
    // }).then (data => {
    //     //this.setState({
    //     //fileprogress: 1.0,
    //     //})
    // })

})()