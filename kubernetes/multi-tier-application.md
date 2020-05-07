# Deploying a multi-tier application

In this chapter we are going to deploy a multi-tier micro-services oriented application called `PowerApp`, the application was built for the purpose of this workshop. The application is composed by a `frontend` in `PHP` served by apache, by a `backend` service in `nodejs` and a `mongodb` database. It's a simple listing application where you just add items to your list and they will get persisted.

## Goal of this section

The end goal of this section is to (obviously) have the application running. In order to achieve that, we will have to deploy all the services with their configurations and secrets. Once that is done, we will need to setup the communication between them.  

The only pod exposed to the outside world is the `frontend`. We will do this in two flavors: via `NodePort` and with an `Ingress`.

## Prerequisites: ConfigMaps & Secrets

If you have a look at `deployments/web-deployment.yaml` you will see that states the follow:

```yaml
…
env:
- name: COMPANY
  valueFrom:
    configMapKeyRef:
      name: web
      key: COMPANY
# How to use a Secret
- name: SOME_PASSWORD
  valueFrom:
    secretKeyRef:
      name: web
      key: some-password
…
```

This means that the deployment depends on both a ConfigMap called `web` and a Secret called `web`.

If we try to issue `kubectl apply -f deployments/web-deployment.yaml` without having created the ConfigMap and the Secret beforehand, it will result in a failure.

```bash
kubectl get pods

NAME                            READY     STATUS                       RESTARTS   AGE
powerapp-web-7c675467d8-727cm   0/1       CreateContainerConfigError   0          1m
powerapp-web-7c675467d8-bl8zv   0/1       CreateContainerConfigError   0          1m
powerapp-web-7c675467d8-fc4rr   0/1       CreateContainerConfigError   0          1m
```

As first step we **must** create our `ConfigMaps` and `Secrets`.

```bash
kubectl apply -f configmaps/powerapp-configmap.yaml
configmap "web" created

kubectl apply -f secrets/powerapp-secrets.yaml
secret "web" created
kubectl get configmaps
NAME      DATA      AGE
web       2         15s

kubectl get secrets
NAME                  TYPE                                  DATA      AGE
default-token-p5g8v   kubernetes.io/service-account-token   3         26s
web                   Opaque                                1         11s
```

## Deploying our applications

We can start rolling out our applications in the following way

```bash
kubectl apply -f deployments/backend-deployment.yaml
kubectl apply -f deployments/web-deployment.yaml
kubectl apply -f deployments/mongo-deployment.yaml

NAME                                READY     STATUS              RESTARTS   AGE
powerapp-backend-106957089-jcw91    0/1       ContainerCreating   0          1m
powerapp-mongodb-2965042848-blp9z   0/1       ContainerCreating   0          43s
powerapp-web-1507534023-d7dvb       0/1       ContainerCreating   0          2m
powerapp-web-1507534023-nxz6c       0/1       ContainerCreating   0          2m
powerapp-web-1507534023-rndj6       0/1       ContainerCreating   0          2m
```

We will soon see that all the deployments end up correctly **except for mongodb**. 

## What is happening?

If we want to check the status of what is happening in our cluster, we should use:  

`kubectl describe pod <pod_name>`  
`kubectl describe deployment <deployment_name>`  
`kubectl rollout status deployment/<deployment_name>`  

Can you guess why our mongodb won't get running?

## Volumes

Mongodb is failing because we forgot one of its dependencies: `the volume`. One of the characteristics of Kubernetes is that it lets you handle storage in terms of volumes that your pods will claim when they get scheduled. In this specific case, `mongodb` is requiring to have a volume available, which currently does NOT exists.

We can create it:  

`kubectl apply -f volumes/kubeprimer-db-persistentvolumeclaim.yaml`

Nice! We have all our deployments correctly getting rolled out and if there are no issues we should see the pods soon getting `Running`.

yet, we have no idea if the application is running or not and we have no idea if the containers are seeing each others or not.

## Services

Now we can start to expose the pods to the outer world and to each others.

Let's start with `web`. As I mentioned this is the only pod that will be reachable from outside the cluster.

```bash
kubectl apply -f services/web-service-nodeport.yaml
```

Now, if you do `minikube ip` you can get the IP of the minikube node exposing the service. To get what port is exposing our service, we can use `kubectl get services`. Now visiting `<minikube_ip>:<port>` should show the frontend application.

You are seeing the `NodePort` service in action, where each node of the cluster will expose a random port and serve traffic from your pod there.

We can now rollout services for `backend` and `mongodb` in a similar way. Once that is done, reloading the frontend page should show no error and magically our application works.

```bash
kubectl apply -f services/backend-service.yaml
kubectl apply -f services/mongo-service.yaml
```

But what is really happening here? Let's discuss this together.

## Exposing applications via Ingress

So far we have exposed the frontend using `NodePort`, but accessing the service with the combination `<ip>:<port>` isn't exactly ideal. Time to see something more advanced: `Ingress`

`minikube addons enable ingress`

We can apply the ingress as follows:

```bash
kubectl apply -f  ingress/powerapp-ingress.yaml
```
