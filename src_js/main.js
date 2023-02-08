import "./main.scss"

(function (){

    const url = window.location.href

    class FileUploader {
        constructor() {
            this.reader = {}
            this.file = {}
            this.sliceSize = 1000 * 1024 // page size
            this.uploadProgress = document.querySelector('#file-upload-progress')
            const submit = document.querySelector('[name="file-upload-submit"]')
            submit.addEventListener('click', (e) => this.uploadInit(e))
        }

        /**
         * Uploads the file after upload btn click
         * @param event
         */
        uploadInit(event){
            event.preventDefault()

            this.reader = new FileReader()
            this.file = document.querySelector( '#file-upload-input' ).files[0];

            this.uploadFile( 0 );

        }

        /**
         * Uploads the file recursively and calls moveUploadedFile,
         * once upload is complete.
         * @param start
         */
        uploadFile(start) {
            let nextSlice = start + this.sliceSize + 1;
            let blob = this.file.slice( start, nextSlice );

            this.reader.onerror = (event) => {
                console.warn('+********************* ERROR *******************')
                console.warn(event)
                console.warn('+********************* ERROR *******************')
            }

            this.reader.onload = async (event) => {

                const data = {
                    file_data: event.target.result,
                    file: this.file.name,
                    file_type: this.file.type,
                    content_length: this.file.size,
                    nonce: "a secure token to verify request"
                }

                const headers =  {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }

                const res = await fetch(url, {
                    method: 'POST',
                    mode: 'same-origin',
                    cache: 'no-cache',
                    credentials: 'same-origin',
                    headers,
                    referrerPolicy: 'same-origin',
                    body: JSON.stringify(data)
                })
                // Success fetch
                if ( res.ok ) {
                    let sizeDone = start + this.sliceSize;
                    let fractionDone = sizeDone / this.file.size
                    let percentDone = Math.floor( fractionDone * 100 );

                    // Upload remaining parts
                    if ( nextSlice < this.file.size ) {

                        // Update upload progress
                        this.uploadProgress.querySelector('label').innerText = `File upload progress:  ${percentDone}%`
                        this.uploadProgress.querySelector('progress').innerText = `${percentDone}%`
                        this.uploadProgress.querySelector('progress').value = percentDone

                        // Upload till the last slice.
                        this.uploadFile(nextSlice)
                    } else {

                        // Upload complete.
                        this.uploadProgress.querySelector('progress').innerText = `File upload progress:  ${percentDone}%`
                        this.uploadProgress.querySelector('progress').value = percentDone
                        this.uploadProgress.querySelector('label').innerText = 'Upload Complete!'


                        // Move file from local storage to aws s3 bucket.
                        await this.moveUploadedFile()

                    }

                }
                else {
                    console.warn("Failed to upload file!!!")
                }


            }


            this.reader.readAsDataURL( blob );

        }

        /**
         * Instructs the server to move the uploaded file.
         */
        async moveUploadedFile(){
            console.log('Moving the file now.........')
            const data = {
                move_uploaded: true,
                file: this.file.name,
                file_type: this.file.type,
                content_length: this.file.size,
                nonce: "a secure token to verify request"
            }

            const headers =  {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }

            const res = await fetch(url, {
                method: 'POST',
                mode: 'same-origin',
                cache: 'no-cache',
                credentials: 'same-origin',
                headers,
                referrerPolicy: 'same-origin',
                body: JSON.stringify(data)
            })

            if (res.ok) {
                console.log('File moved and deleted..................')
            }


        }
    }

    const test = new FileUploader()

})()