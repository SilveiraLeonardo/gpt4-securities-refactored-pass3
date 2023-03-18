
#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include<limits.h>

struct Image
{
    char header[5];
    int width;
    int height;
    char data[11];
};

long getFileSize(FILE *file)
{
    long originalPos = ftell(file);
    fseek(file, 0, SEEK_END);
    long size = ftell(file);
    fseek(file, originalPos, SEEK_SET);
    return size;
}

int ProcessImage(char* filename){

    FILE *fp;
    struct Image img;

    fp = fopen(filename,"rb");
    if(fp == NULL)
    {
        printf("\nCan't open file or file doesn't exist.");
        exit(0);
    }

    long fileSize = getFileSize(fp);
    if (fileSize != sizeof(struct Image))
    {
        printf("\nInvalid file size.");
        fclose(fp);
        exit(0);
    }

    printf("\n\tHeader\twidth\theight\tdata\t\r\n");

    while(fread(&img,sizeof(img),1,fp)>0){
        img.header[4] = '\0';
        img.data[10] = '\0';
        printf("\n\t%s\t%d\t%d\t%s\r\n",img.header,img.width,img.height,img.data);

        int size1 = img.width + img.height;
        if (size1 > 0 && size1 < 1024){
            char* buff1=(char*)malloc(size1+1);
            if (buff1 != NULL){
                if (size1 > strlen(img.data)){
                    size1 = strlen(img.data);
                }
                strncpy(buff1, img.data, size1);
                buff1[size1] = '\0';
                free(buff1);
            }
        }

        int size2 = img.width - img.height + 100;
        if (size2 > 0 && size2 < 1024){
            char* buff2=(char*)malloc(size2+1);
            if (buff2 != NULL){
                if (size2 > strlen(img.data)){
                    size2 = strlen(img.data);
                }
                strncpy(buff2, img.data, size2);
                buff2[size2] = '\0';
                free(buff2);
            }
        }

        int size3;
        if (img.height != 0)
        {
            size3 = img.width / img.height;
        }
        else
        {
            size3 = 0;
        }

        if (size3 > 0 && size3 < 1024){
            char* buff4 =(char*)malloc(size3+1);
            if (buff4 != NULL){
                if (size3 > strlen(img.data)){
                    size3 = strlen(img.data);
                }
                strncpy(buff4, img.data, size3);
                buff4[size3] = '\0';
                free(buff4);
            }
        }
    }
    fclose(fp);
}

int main(int argc,char **argv)
{
    if (argc > 1){
        if (strspn(argv[1], "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_") == strlen(argv[1])){
            ProcessImage(argv[1]);
        }
        else{
            printf("Invalid input");
            exit(0);
        }
    }
    else{
        printf("Input too long");
        exit(0);
    }
}
