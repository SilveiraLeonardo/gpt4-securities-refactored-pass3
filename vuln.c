
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

int ProcessImage(char* filename){

  FILE *fp;
  char ch;
  struct Image img;

  if (strlen(filename) >= PATH_MAX){
    printf("Input too long");
    exit(0);
  }

  if (strspn(filename, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_") != strlen(filename)){
    printf("Invalid input");
    exit(0);
  }

  fp = fopen(filename,"r"); 

  if(fp == NULL)
  {
    printf("\nCan't open file or file doesn't exist.");
    exit(0);
  }

  printf("\n\tHeader\tWidth\tHeight\tData\n");

  while(1){
    memset(&img, 0, sizeof(img));
    if (fread(&img,sizeof(img),1,fp) != 1)
    {
      break;
    }
    img.data[10] = '\0';

    if (img.width < 0 || img.height < 0)
    {
      printf("Invalid image dimensions");
      exit(0);
    }

    printf("\n\t%s\t%d\t%d\t%s\n",img.header,img.width,img.height,img.data);

    if (INT_MAX - img.width < img.height || INT_MIN + img.width > img.height){
      printf("Size1 calculation might result in integer overflow.");
      exit(0);
    }

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

    if (INT_MIN + 100 > img.width - img.height){
      printf("Size2 calculation might result in integer overflow.");
      exit(0);
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

    if (img.height != 0)
    {
      int size3 = img.width / img.height;
      if (size3 > 0 && size3 < 1024)
      {
        char* buff4 =(char*)malloc(size3+1);
        if (buff4 != NULL)
        {
          if (size3 > strlen(img.data))
          {
            size3 = strlen(img.data);
          }
          strncpy(buff4, img.data, size3);
          buff4[size3] = '\0';
          free(buff4);
        }
      }
    }
    else
    {
      printf("Size3 calculation might result in division by zero.");
      exit(0);
    }
  }
  fclose(fp);

  return 0; // success
}

int main(int argc,char **argv)
{
  if (argc > 1){
    return ProcessImage(argv[1]);
  }
  return 1; // No input provided
}
