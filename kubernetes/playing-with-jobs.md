# Playing with Jobs

In `kubernetes/jobs/` you will find a simple example of job. This is a dummy job, meaning that it doesn't do any real work. It will boot a pod that will calculate `pi`, it will take about 10 sec to complete.

We can deploy the job by running:  
`kubectl apply -f jobs/job.yaml`

Output is going to be:

```bash
kubectl get pods
NAME       READY     STATUS    RESTARTS   AGE
pi-d9s7s   1/1       Running   0          <invalid>
```

The pod will show up as running until the job gets to completion, after that the process will end and kubernetes will consider the job as concluded.

## Parallelism

Parallelism on jobs can be achieved setting `.spec.completions` and `.spec.parallelism`. Playing with these two flags will let us tune how many times the job should run (in case we want additional workers) and how many concurrent jobs should be executed at a time.

1. We can now try to decomment `.spec.completions` in the same file. This is going to tell Kubernetes that `job.yaml` should be executed 10 times.

```bash
kubectl delete job pi
kubectl apply -f jobs/job.yaml
```

What we will see is that one after another, the job will be executed 10 times. Kubernetes is executing them one after the other as `parallelism` is not set and therefore defaulting to 1.

2. We can no decomment `parallelism` too and re-executing the jobs.

```bash
kubectl delete job pi
kubectl apply -f jobs/job.yaml

NAME       READY     STATUS              RESTARTS   AGE
pi-j1b1f   0/1       ContainerCreating   0          <invalid>
pi-rl81p   1/1       Running             0          <invalid>
pi-xd9k1   0/1       ContainerCreating   0          <invalid>
```

You can see that this time it is executing them 3 at a time.

## Failures

If a job fails to complete, by default kubernetes will keep trying executing it forever. In order to set a maximum time during which kubernetes will do retries, you should set the `.spec.activeDeadlineSeconds`.

## Cleanup

As you can see from above, we have to manually delete a job every time it gets completed. This was done on purpose, keeping them around will allow to inspect logs and lifecycle of the pod.
